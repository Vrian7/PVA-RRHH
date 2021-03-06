<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->command->info('Unguarding models');
		Model::unguard();

		$tables = [
			'charges',
			'cities',
			'companies',
			'company_accounts',
			'company_addresses',
			'contract_types',
			'contract_modes',
			'contracts',
			'dependency_position_group',
			'dependency_positions',
			'document_types',
			'documents',
			'employees',
			'employer_numbers',
			'insurance_companies',
			'job_schedules',
			'management_entities',
			'permission_role',
			'permission_user',
			'permissions',
			'position_groups',
			'positions',
			'retirement_reasons',
			'role_user',
			'roles',
			'users',
			'employee_discounts',
			'employer_contributions',
			'employer_tributes',
			'months',
			'procedures',
			'payrolls',
			'consultant_procedures',
			'consultant_payrolls',
			'user_actions',
		];

		$this->command->info('Truncating existing tables');
		foreach ($tables as $table) {
			DB::statement('TRUNCATE TABLE ' . $table . ' CASCADE;');
		}

		$this->call(CitySeeder::class);
		$this->call(InsuranceCompanySeeder::class);
		$this->call(ManagementEntitySeeder::class);
		$this->call(DocumentTypeSeeder::class);
		$this->call(DocumentSeeder::class);
		$this->call(CompanySeeder::class);
		$this->call(CompanyAddressSeeder::class);
		$this->call(PositionGroupSeeder::class);
		$this->call(ChargeSeeder::class);
		$this->call(PositionSeeder::class);
		$this->call(ConsultantPositionSeeder::class);
		$this->call(EmployeeSeeder::class);
		$this->call(UserSeeder::class);
		$this->call(JobScheduleSeeder::class);
		$this->call(RetirementReasonSeeder::class);
		$this->call(ContractTypeSeeder::class);
		$this->call(ContractModeSeeder::class);
		$this->call(ContractSeeder::class);
		$this->call(ConsultantContractSeeder::class);
		$this->call(EmployeeDiscountSeeder::class);
		$this->call(EmployerContributionSeeder::class);
		$this->call(EmployerTributeSeeder::class);
		$this->call(MonthSeeder::class);
		$this->call(ProcedureSeeder::class);
		$this->call(PayrollSeeder::class);
		$this->call(ConsultantProcedureSeeder::class);
		$this->call(ConsultantPayrollSeeder::class);
		$this->call(UserActionSeeder::class);
		$this->call(DocumentTypeAdd2Seeder::class);
		$this->call(DepartureTypeSeeder::class);
		$this->call(DepartureReasonSeeder::class);
		$this->call(ContractTypeDepartureReasonSeeder::class);
	}
}
