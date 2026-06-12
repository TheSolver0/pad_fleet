<?php

namespace Database\Seeders\Support;

use Carbon\Carbon;
use RuntimeException;

class PadDocumentParsers
{
    public static function personnelDriversPath(): string
    {
        return base_path('doc/Liste du personnel chauffeur PAD-1.docx');
    }

    public static function drivingLicensesPath(): string
    {
        return base_path('doc/Liste des Permis de conduire chauffeurs-2025.docx');
    }

    public static function vehicleParkPath(): string
    {
        return base_path('doc/PARC AUTOMOBILE DU PAD AU 01-09-2025.xlsx');
    }

    /**
     * @return list<array{matricule: string, full_name: string, structure: ?string, first_name: string, last_name: string}>
     */
    public static function parsePersonnelDrivers(?string $path = null): array
    {
        $path ??= self::personnelDriversPath();
        $lines = DocxReader::lines($path);
        $start = self::indexAfter($lines, 'Structure du chauffeur');

        if ($start === null) {
            throw new RuntimeException('En-tête introuvable dans la liste du personnel chauffeur.');
        }

        $drivers = [];
        $i = $start;

        while ($i < count($lines)) {
            if (! preg_match('/^\d+$/', $lines[$i])) {
                $i++;
                continue;
            }

            $i++;
            if ($i >= count($lines)) {
                break;
            }

            $fullName = trim($lines[$i++]);
            if ($i >= count($lines)) {
                break;
            }

            $matricule = self::extractMatricule($lines[$i++]);
            if ($matricule === null) {
                continue;
            }

            $structure = null;
            if ($i < count($lines) && ! preg_match('/^\d+$/', $lines[$i]) && ! self::isTableHeader($lines[$i])) {
                $structure = trim($lines[$i]);
                $i++;
            }

            $nameParts = self::splitName($fullName);
            $drivers[$matricule] = [
                'matricule' => $matricule,
                'full_name' => $fullName,
                'structure' => $structure,
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
            ];
        }

        return array_values($drivers);
    }

    /**
     * @return list<array{matricule: string, full_name: string, structure: ?string, licenses: list<array{category: ?string, license_number: string, expiry_date: ?Carbon, notes: ?string}>}>
     */
    public static function parseDrivingLicenses(?string $path = null): array
    {
        $path ??= self::drivingLicensesPath();
        $lines = DocxReader::lines($path);
        $start = self::indexAfter($lines, 'Expiration');

        if ($start === null) {
            throw new RuntimeException('En-tête introuvable dans la liste des permis de conduire.');
        }

        $records = [];
        $i = $start;

        while ($i < count($lines)) {
            if (! preg_match('/^\d+$/', $lines[$i])) {
                $i++;
                continue;
            }

            $i++;
            if ($i + 2 >= count($lines)) {
                break;
            }

            $fullName = trim($lines[$i++]);
            $matricule = self::extractMatricule($lines[$i++]);
            if ($matricule === null) {
                continue;
            }

            $structure = trim($lines[$i++]);
            if (! isset($records[$matricule])) {
                $records[$matricule] = [
                    'matricule' => $matricule,
                    'full_name' => $fullName,
                    'structure' => $structure !== '' ? $structure : null,
                    'licenses' => [],
                ];
            }

            while ($i < count($lines) && ! self::isNextDriverRecord($lines, $i)) {
                $category = trim($lines[$i]);

                if (self::isUnavailableLicenseNote($category)) {
                    self::appendLicense($records[$matricule]['licenses'], [
                        'category' => null,
                        'license_number' => "{$matricule}-UNAVAILABLE",
                        'expiry_date' => null,
                        'notes' => $category,
                    ]);
                    $i++;
                    continue;
                }

                if ($i + 1 >= count($lines)) {
                    break;
                }

                $second = trim($lines[$i + 1]);
                $third = $i + 2 < count($lines) ? trim($lines[$i + 2]) : null;

                if (self::isFrenchDate($second)) {
                    self::appendLicense($records[$matricule]['licenses'], [
                        'category' => $category,
                        'license_number' => self::syntheticLicenseNumber($matricule, $category, $second),
                        'expiry_date' => self::parseFrenchDate($second),
                        'notes' => null,
                    ]);
                    $i += 2;
                    continue;
                }

                if ($third !== null && self::isFrenchDate($third)) {
                    self::appendLicense($records[$matricule]['licenses'], [
                        'category' => $category,
                        'license_number' => $second !== '' ? $second : self::syntheticLicenseNumber($matricule, $category, $third),
                        'expiry_date' => self::parseFrenchDate($third),
                        'notes' => null,
                    ]);
                    $i += 3;
                    continue;
                }

                $i++;
            }
        }

        return array_values($records);
    }

    /**
     * @return array{first_name: string, last_name: string}
     */
    public static function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];

        if ($parts === []) {
            return ['first_name' => '', 'last_name' => ''];
        }

        if (count($parts) === 1) {
            return ['first_name' => $parts[0], 'last_name' => ''];
        }

        $lastName = array_shift($parts);

        return [
            'last_name' => $lastName,
            'first_name' => implode(' ', $parts),
        ];
    }

    private static function indexAfter(array $lines, string $needle): ?int
    {
        foreach ($lines as $index => $line) {
            if ($line === $needle) {
                return $index + 1;
            }
        }

        return null;
    }

    private static function isTableHeader(string $line): bool
    {
        return in_array($line, ['N°', 'Nom et prénom', 'Nom et Prénom', 'Matricule', 'Structure du chauffeur', 'catégorie', 'Numéro du permis', 'Expiration'], true);
    }

    private static function isUnavailableLicenseNote(string $value): bool
    {
        $normalized = mb_strtolower($value);

        return str_contains($normalized, 'pièces non disponible')
            || str_contains($normalized, 'pieces non disponible')
            || $normalized === 'non';
    }

    private static function isNextDriverRecord(array $lines, int $index): bool
    {
        if (! preg_match('/^\d+$/', $lines[$index])) {
            return false;
        }

        return isset($lines[$index + 2]) && self::extractMatricule($lines[$index + 2]) !== null;
    }

    private static function syntheticLicenseNumber(string $matricule, string $category, string $expiry): string
    {
        $safeCategory = strtoupper(preg_replace('/[^A-Z0-9]+/', '', $category) ?: 'X');
        $safeExpiry = str_replace('/', '-', $expiry);

        return "{$matricule}-{$safeCategory}-{$safeExpiry}";
    }

    private static function isFrenchDate(string $value): bool
    {
        return (bool) preg_match('/^\d{2}\/\d{2}\/\d{4}$/', trim($value));
    }

    private static function parseFrenchDate(string $value): Carbon
    {
        return Carbon::createFromFormat('d/m/Y', trim($value))->startOfDay();
    }

    private static function extractMatricule(string $value): ?string
    {
        $value = strtoupper(trim($value));

        if (preg_match('/(\d{4,5})\s*-\s*([A-Z])/', $value, $matches)) {
            return $matches[1].'-'.$matches[2];
        }

        if (preg_match('/(\d{4,5})\s+([A-Z])\b/', $value, $matches)) {
            return $matches[1].'-'.$matches[2];
        }

        return null;
    }

    /**
     * @param  list<array{category: ?string, license_number: string, expiry_date: ?Carbon, notes: ?string}>  $licenses
     * @param  array{category: ?string, license_number: string, expiry_date: ?Carbon, notes: ?string}  $license
     */
    private static function appendLicense(array &$licenses, array $license): void
    {
        foreach ($licenses as $existing) {
            if ($existing['license_number'] === $license['license_number']) {
                return;
            }
        }

        $licenses[] = $license;
    }
}
