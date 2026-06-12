<?php

namespace Database\Seeders\Support;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use RuntimeException;

class VehicleParkParser
{
    private const HEADER_ROW = 26;

    private const COL_AFFECTATION = 3;

    private const COL_ASSIGNMENT_TYPE = 5;

    private const COL_MODEL = 6;

    private const COL_CHASSIS = 7;

    private const COL_REGISTRATION = 8;

    private const COL_STATUS = 9;

    private const COL_POWER = 10;

    private const COL_PURCHASE_YEAR = 11;

    private const COL_PURCHASE_PRICE = 12;

    private const COL_CARTE_DELIVRANCE = 14;

    /**
     * @return list<array{
     *     registration: string,
     *     model_label: string,
     *     chassis_number: ?string,
     *     assignment_holder: ?string,
     *     assignment_type: ?string,
     *     status: string,
     *     power: ?int,
     *     purchase_date: ?Carbon,
     *     purchase_price: ?float,
     *     category: ?string,
     *     notes: ?string
     * }>
     */
    public static function parse(?string $path = null): array
    {
        $path ??= PadDocumentParsers::vehicleParkPath();

        if (! is_file($path)) {
            throw new RuntimeException("Fichier introuvable : {$path}");
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('Feuil1') ?? $spreadsheet->getActiveSheet();

        $vehicles = [];
        $seenRegistrations = [];

        for ($row = self::HEADER_ROW + 1; $row <= $sheet->getHighestRow(); $row++) {
            $registration = self::normalizeRegistration(self::cellValue($sheet, $row, self::COL_REGISTRATION));
            if ($registration === null) {
                continue;
            }

            if (isset($seenRegistrations[$registration])) {
                continue;
            }

            $modelLabel = trim((string) self::cellValue($sheet, $row, self::COL_MODEL));
            if ($modelLabel === '') {
                continue;
            }

            $seenRegistrations[$registration] = true;
            $vehicles[] = [
                'registration' => $registration,
                'model_label' => $modelLabel,
                'chassis_number' => self::normalizeText(self::cellValue($sheet, $row, self::COL_CHASSIS)),
                'assignment_holder' => self::normalizeText(self::cellValue($sheet, $row, self::COL_AFFECTATION)),
                'assignment_type' => self::mapAssignmentType(self::cellValue($sheet, $row, self::COL_ASSIGNMENT_TYPE)),
                'status' => self::mapStatus(self::cellValue($sheet, $row, self::COL_STATUS)),
                'power' => self::parsePower(self::cellValue($sheet, $row, self::COL_POWER)),
                'purchase_date' => self::parsePurchaseDate(
                    self::cellValue($sheet, $row, self::COL_PURCHASE_YEAR),
                    self::cellValue($sheet, $row, self::COL_CARTE_DELIVRANCE),
                ),
                'purchase_price' => self::parsePrice(self::cellValue($sheet, $row, self::COL_PURCHASE_PRICE)),
                'category' => self::guessCategory($modelLabel),
                'notes' => self::buildNotes(
                    self::cellValue($sheet, $row, self::COL_AFFECTATION),
                    self::cellValue($sheet, $row, self::COL_ASSIGNMENT_TYPE),
                    self::cellValue($sheet, $row, 4),
                ),
            ];
        }

        return $vehicles;
    }

    public static function resolveBrandAndModel(string $modelLabel): array
    {
        $label = trim($modelLabel);
        $upper = mb_strtoupper($label);

        $knownBrands = [
            'TOYOTA', 'PEUGEOT', 'RENAULT', 'NISSAN', 'HYUNDAI', 'KIA', 'MERCEDES', 'MERCEDES-BENZ',
            'BMW', 'VOLKSWAGEN', 'FORD', 'MITSUBISHI', 'SUZUKI', 'ISUZU', 'CHEVROLET', 'FIAT',
            'DACIA', 'HONDA', 'MAZDA', 'LAND ROVER', 'JEEP', 'IVECO', 'MAN', 'HINO', 'KINGLONG', 'DEMAG', 'CATERPILLAR',
        ];

        foreach ($knownBrands as $brand) {
            if (str_starts_with($upper, $brand.' ') || $upper === $brand) {
                $model = trim(substr($label, strlen($brand)));

                return [
                    'brand' => self::normalizeBrandName($brand),
                    'model' => $model !== '' ? $model : 'Inconnu',
                ];
            }
        }

        if (preg_match('/\b(HILUX|PRADO|COROLLA|HIACE|RAV4|LC\d+|LAND CRUISER|CAMRY|YARIS|FORTUNER|RUSH)\b/i', $label, $matches)) {
            return ['brand' => 'Toyota', 'model' => self::normalizeModelName($matches[1])];
        }

        if (str_starts_with($upper, 'LC')) {
            return ['brand' => 'Toyota', 'model' => self::normalizeModelName($label)];
        }

        if (str_contains($upper, 'BUS')) {
            return ['brand' => 'Kinglong', 'model' => $label];
        }

        if (str_contains($upper, 'CAMION')) {
            $parts = preg_split('/\s+/', $label) ?: [];
            $brand = count($parts) >= 2 ? ucfirst(mb_strtolower($parts[1])) : 'Autre';

            return ['brand' => $brand, 'model' => $label];
        }

        if (str_contains($upper, 'GRUE')) {
            return ['brand' => 'Demag', 'model' => $label];
        }

        return ['brand' => 'Autre', 'model' => $label];
    }

    private static function cellValue($sheet, int $row, int $column): mixed
    {
        return $sheet->getCell([$column, $row])->getCalculatedValue();
    }

    private static function normalizeRegistration(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $registration = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $value)));

        return preg_match('/^[A-Z]{2}\s?\d+\s?[A-Z]{2}$/', $registration) ? $registration : null;
    }

    private static function normalizeText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }

    private static function mapAssignmentType(mixed $value): ?string
    {
        $text = mb_strtolower(trim((string) ($value ?? '')));
        if ($text === '') {
            return null;
        }

        return match (true) {
            str_contains($text, 'dotation') => 'dotation',
            str_contains($text, 'lucatelli') => 'lucatelli',
            str_contains($text, 'liaison') => 'liaison',
            str_contains($text, 'mission') => 'missions',
            str_contains($text, 'transport') && str_contains($text, 'vip') => 'transport_vip',
            str_contains($text, 'travaux') => 'travaux',
            str_contains($text, 'séc') || str_contains($text, 'surete') || str_contains($text, 'sûreté') => 'sec_surete',
            str_contains($text, 'affectation') => 'affectation',
            default => null,
        };
    }

    private static function mapStatus(mixed $value): string
    {
        $text = mb_strtoupper(trim((string) ($value ?? '')));

        return str_contains($text, 'SERVICE') ? 'available' : 'out_of_service';
    }

    private static function parsePower(mixed $value): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (preg_match('/(\d+)/', (string) $value, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private static function parsePurchaseDate(mixed $yearValue, mixed $deliveredAtValue): ?Carbon
    {
        if ($deliveredAtValue !== null && $deliveredAtValue !== '') {
            if ($deliveredAtValue instanceof \DateTimeInterface) {
                return Carbon::instance($deliveredAtValue)->startOfDay();
            }

            if (is_numeric($deliveredAtValue)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $deliveredAtValue))->startOfDay();
            }

            $parsed = self::parseFlexibleDate((string) $deliveredAtValue);
            if ($parsed !== null) {
                return $parsed;
            }
        }

        if ($yearValue instanceof \DateTimeInterface) {
            return Carbon::instance($yearValue)->startOfDay();
        }

        if (is_numeric($yearValue)) {
            if ((float) $yearValue > 3000) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $yearValue))->startOfDay();
            }

            return Carbon::createFromDate((int) $yearValue, 1, 1)->startOfDay();
        }

        if (is_string($yearValue) && preg_match('/^\d{4}$/', trim($yearValue))) {
            return Carbon::createFromDate((int) trim($yearValue), 1, 1)->startOfDay();
        }

        return null;
    }

    private static function parseFlexibleDate(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^(\d{2})\/(\d{2})(\d{4})$/', $value, $matches)) {
            return Carbon::createFromDate((int) $matches[3], (int) $matches[2], (int) $matches[1])->startOfDay();
        }

        foreach (['Y-m-d', 'd/m/Y', 'm/d/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->startOfDay();
            } catch (\Throwable) {
            }
        }

        return null;
    }

    private static function parsePrice(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $parsed = parse_french_number((string) $value);

        return $parsed !== null ? (float) $parsed : null;
    }

    private static function guessCategory(string $modelLabel): ?string
    {
        $upper = mb_strtoupper($modelLabel);

        return match (true) {
            str_contains($upper, 'MOTO') || str_contains($upper, 'YAMAHA') || str_contains($upper, 'PIAGGIO') => 'moto',
            str_contains($upper, 'BUS') || str_contains($upper, 'KINGLONG') => 'bus',
            str_contains($upper, 'CAMION') || str_contains($upper, 'MAN TGS') || str_contains($upper, 'HINO') || str_contains($upper, 'IVECO') => 'lourd',
            str_contains($upper, 'GRUE') || str_contains($upper, 'CATERPILLAR') => 'utilitaire',
            str_contains($upper, 'HILUX') => 'camionnette',
            str_contains($upper, 'PRADO') || str_contains($upper, 'LAND CRUISER') || str_contains($upper, 'LC') || str_contains($upper, '4X4') => '4x4',
            default => 'leger',
        };
    }

    private static function buildNotes(mixed $holder, mixed $assignmentType, mixed $subHolder): ?string
    {
        $parts = array_filter([
            self::normalizeText($holder) ? 'Détenteur: '.self::normalizeText($holder) : null,
            self::normalizeText($subHolder) ? 'SAP: '.self::normalizeText($subHolder) : null,
            self::normalizeText($assignmentType) ? 'Type source: '.self::normalizeText($assignmentType) : null,
        ]);

        return $parts === [] ? null : implode(' | ', $parts);
    }

    private static function normalizeBrandName(string $brand): string
    {
        return match (strtoupper($brand)) {
            'MERCEDES', 'MERCEDES-BENZ' => 'Mercedes-Benz',
            default => ucwords(mb_strtolower($brand)),
        };
    }

    private static function normalizeModelName(string $model): string
    {
        $model = trim($model);

        return match (strtoupper($model)) {
            'LC300', 'LC VX.R', 'LC VX R' => 'Land Cruiser',
            default => ucwords(mb_strtolower($model)),
        };
    }
}
