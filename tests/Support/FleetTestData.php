<?php

namespace Tests\Support;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Assureur;
use App\Models\Brand;
use App\Models\Demandeur;
use App\Models\Department;
use App\Models\Direction;
use App\Models\Driver;
use App\Models\Garage;
use App\Models\InsuranceContractGlobal;
use App\Models\Mechanic;
use App\Models\Person;
use App\Models\Repair;
use App\Models\Stock;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Database\Seeders\RolesAndPermissionsSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FleetTestData
{
    public static function user(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /** @return array{0: Brand, 1: VehicleModel} */
    public static function brandAndModel(): array
    {
        $brand = Brand::create(['name' => 'Toyota', 'code' => 'TOY']);
        $model = VehicleModel::create([
            'brand_id' => $brand->id,
            'name' => 'Hilux',
            'code' => 'HILUX',
        ]);

        return [$brand, $model];
    }

    public static function vehicle(?VehicleModel $model = null, array $attributes = []): Vehicle
    {
        if ($model === null) {
            [, $model] = self::brandAndModel();
        }

        return Vehicle::create(array_merge([
            'registration' => 'LT-001-AA',
            'vehicle_model_id' => $model->id,
            'mileage' => 10_000,
            'status' => Vehicle::STATUS_AVAILABLE,
        ], $attributes));
    }

    public static function direction(array $attributes = []): Direction
    {
        return Direction::create(array_merge([
            'name' => 'Direction Générale',
            'code' => 'DG',
        ], $attributes));
    }

    public static function department(Direction $direction, array $attributes = []): Department
    {
        return Department::create(array_merge([
            'direction_id' => $direction->id,
            'name' => 'Département Logistique',
            'code' => 'LOG',
        ], $attributes));
    }

    public static function assureur(array $attributes = []): Assureur
    {
        return Assureur::create(array_merge([
            'name' => 'AXA Cameroun',
            'code' => 'AXA',
            'is_active' => true,
        ], $attributes));
    }

    public static function insuranceContract(?Assureur $assureur = null, array $attributes = []): InsuranceContractGlobal
    {
        $assureur ??= self::assureur();

        return InsuranceContractGlobal::create(array_merge([
            'name' => 'Marché flotte 2026',
            'assureur_id' => $assureur->id,
            'insurer' => $assureur->name,
            'start_date' => '2026-01-01',
            'end_date' => '2027-01-01',
        ], $attributes));
    }

    public static function demandeur(array $attributes = []): Demandeur
    {
        return Demandeur::create(array_merge([
            'name' => 'Jean Demandeur',
            'demandeur_type' => Demandeur::TYPE_PERSON,
            'contact_phone' => '+237600000000',
        ], $attributes));
    }

    public static function person(array $attributes = []): Person
    {
        return Person::create(array_merge([
            'name' => 'Personne Test',
            'email' => 'personne@example.com',
        ], $attributes));
    }

    public static function driver(array $attributes = []): Driver
    {
        return Driver::create(array_merge([
            'first_name' => 'Paul',
            'last_name' => 'Chauffeur',
            'phone' => '+237633333333',
            'is_available' => true,
        ], $attributes));
    }

    public static function seedRolesAndPermissions(): void
    {
        if (Permission::where('guard_name', 'web')->exists()) {
            return;
        }

        (new RolesAndPermissionsSeeder)->run();
    }

    public static function userWithPermission(string $permission): User
    {
        self::seedRolesAndPermissions();

        $user = self::user();
        $user->givePermissionTo($permission);

        return $user;
    }

    public static function userWithRole(string $role): User
    {
        self::seedRolesAndPermissions();

        $user = self::user();
        $user->assignRole(Role::findByName($role, 'web'));

        return $user;
    }

    public static function articleCategory(array $attributes = []): ArticleCategory
    {
        return ArticleCategory::create(array_merge([
            'name' => 'Pièces détachées',
            'code' => 'PD',
            'is_active' => true,
        ], $attributes));
    }

    public static function article(?ArticleCategory $category = null, array $attributes = []): Article
    {
        $category ??= self::articleCategory();

        return Article::create(array_merge([
            'reference' => 'ART-001',
            'name' => 'Filtre à huile',
            'article_category_id' => $category->id,
            'unit' => 'unité',
            'purchase_price' => 5000,
            'min_stock_level' => 5,
            'is_active' => true,
        ], $attributes));
    }

    public static function stock(Article $article, int $quantity = 20, string $location = 'garage'): Stock
    {
        return Stock::create([
            'article_id' => $article->id,
            'location' => $location,
            'quantity' => $quantity,
            'reserved_quantity' => 0,
        ]);
    }

    public static function garage(array $attributes = []): Garage
    {
        return Garage::create(array_merge([
            'name' => 'Garage Central',
            'type' => 'internal',
            'is_active' => true,
        ], $attributes));
    }

    public static function mechanic(array $attributes = []): Mechanic
    {
        return Mechanic::create(array_merge([
            'first_name' => 'Marc',
            'last_name' => 'Mécano',
            'is_active' => true,
        ], $attributes));
    }

    public static function repair(?Vehicle $vehicle = null, ?Garage $garage = null, array $attributes = []): Repair
    {
        $vehicle ??= self::vehicle();
        $garage ??= self::garage();

        return Repair::create(array_merge([
            'vehicle_id' => $vehicle->id,
            'garage_id' => $garage->id,
            'type' => Repair::TYPE_INTERNAL,
            'description' => 'Réparation test',
            'repair_type' => 'mecanique',
            'priority' => Repair::PRIORITY_MEDIUM,
        ], $attributes));
    }
}
