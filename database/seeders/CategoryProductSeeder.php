<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Mutual Funds Category
        $mutualFund = ProductCategory::updateOrCreate(
            ['code' => 'MUTUAL_FUND'],
            [
                'name' => 'Mutual Fund',
                'description' => 'Investment through mutual fund schemes.',
                'is_active' => true,
            ]
        );

        // Mutual Fund Products (8 products)
        $this->createProducts($mutualFund, [
            ['code' => 'MF-EQUITY-001', 'name' => 'Equity Growth Fund', 'description' => 'High growth potential equity mutual fund.'],
            ['code' => 'MF-DEBT-001', 'name' => 'Debt Fund', 'description' => 'Fixed income securities mutual fund.'],
            ['code' => 'MF-BALANCED-001', 'name' => 'Balanced Fund', 'description' => 'Mix of equity and debt securities.'],
            ['code' => 'MF-GOLD-001', 'name' => 'Gold Fund', 'description' => 'Investment in gold through mutual fund.'],
            ['code' => 'MF-LIQUID-001', 'name' => 'Liquid Fund', 'description' => 'Short-term investment option for liquid cash.'],
            ['code' => 'MF-TARGET-001', 'name' => 'Target Fund', 'description' => 'Retirement focused mutual fund.'],
            ['code' => 'MF-SECTOR-001', 'name' => 'Sector Fund', 'description' => 'Tech sector focused mutual fund.'],
            ['code' => 'MF-INTERNATIONAL', 'name' => 'International Fund', 'description' => 'Global diversification fund.'],
        ]);

        // 2. Health Insurance Category
        $healthInsurance = ProductCategory::updateOrCreate(
            ['code' => 'HEALTH_INSURANCE'],
            [
                'name' => 'Health Insurance',
                'description' => 'Health and medical coverage insurance plans.',
                'is_active' => true,
            ]
        );

        // Health Insurance Products (5 products)
        $this->createProducts($healthInsurance, [
            ['code' => 'HI-INDIVIDUAL', 'name' => 'Individual Health Plan', 'description' => 'Health coverage for individual.'],
            ['code' => 'HI-FAMILY', 'name' => 'Family Health Plan', 'description' => 'Comprehensive family health coverage.'],
            ['code' => 'HI-CRITICAL', 'name' => 'Critical Illness Cover', 'description' => 'Protection against critical illnesses.'],
            ['code' => 'HI-MEDI', 'name' => 'Mediclaim Plan', 'description' => 'Cashless mediclaim insurance.'],
            ['code' => 'HI-SENIOR', 'name' => 'Senior Citizen Plan', 'description' => 'Health coverage for senior citizens.'],
        ]);

        // 3. Life Insurance Category
        $lifeInsurance = ProductCategory::updateOrCreate(
            ['code' => 'LIFE_INSURANCE'],
            [
                'name' => 'Life Insurance',
                'description' => 'Life protection and investment insurance plans.',
                'is_active' => true,
            ]
        );

        // Life Insurance Products (6 products)
        $this->createProducts($lifeInsurance, [
            ['code' => 'LI-TERM', 'name' => 'Term Life Insurance', 'description' => 'Pure life protection for defined term.'],
            ['code' => 'LI-ENDOWMENT', 'name' => 'Endowment Plan', 'description' => 'Combination of insurance and investment returns.'],
            ['code' => 'LI-ULIP', 'name' => 'ULIP Plan', 'description' => 'Unit-linked insurance plan with market-linked returns.'],
            ['code' => 'LI-MONEY-BACK', 'name' => 'Money Back Plan', 'description' => 'Regular income with life protection.'],
            ['code' => 'LI-WHOLE-LIFE', 'name' => 'Whole Life Plan', 'description' => 'Lifelong protection insurance.'],
            ['code' => 'LI-CHILD', 'name' => 'Child Plan', 'description' => 'Education and future security for children.'],
        ]);

        // 4. General Insurance Category
        $generalInsurance = ProductCategory::updateOrCreate(
            ['code' => 'GENERAL_INSURANCE'],
            [
                'name' => 'General Insurance',
                'description' => 'Property, motor, travel and liability insurance.',
                'is_active' => true,
            ]
        );

        // General Insurance Products (4 products)
        $this->createProducts($generalInsurance, [
            ['code' => 'GI-MOTOR', 'name' => 'Motor Insurance', 'description' => 'Comprehensive vehicle insurance coverage.'],
            ['code' => 'GI-HOME', 'name' => 'Home Insurance', 'description' => 'Property and contents insurance.'],
            ['code' => 'GI-TRAVEL', 'name' => 'Travel Insurance', 'description' => 'Travel and vacation protection.'],
            ['code' => 'GI-LIABILITY', 'name' => 'Liability Insurance', 'description' => 'Personal liability coverage.'],
        ]);

        // 5. Loan Products Category
        $loanProducts = ProductCategory::updateOrCreate(
            ['code' => 'LOAN_PRODUCTS'],
            [
                'name' => 'Loan Products',
                'description' => 'Various loan products for different needs.',
                'is_active' => true,
            ]
        );

        // Loan Products (4 products)
        $this->createProducts($loanProducts, [
            ['code' => 'LOAN-PERSONAL', 'name' => 'Personal Loan', 'description' => 'Unsecured personal loan for various needs.'],
            ['code' => 'LOAN-HOME', 'name' => 'Home Loan', 'description' => 'Secured loan for property purchase.'],
            ['code' => 'LOAN-AUTO', 'name' => 'Auto Loan', 'description' => 'Financing for vehicle purchase.'],
            ['code' => 'LOAN-BUSINESS', 'name' => 'Business Loan', 'description' => 'Loan for small and medium business startups.'],
        ]);

        // 6. Other Financial Products Category
        $financialProducts = ProductCategory::updateOrCreate(
            ['code' => 'FINANCIAL_PRODUCTS'],
            [
                'name' => 'Other Financial Products',
                'description' => 'Additional financial services and products.',
                'is_active' => true,
            ]
        );

        // Financial Products (3 products)
        $this->createProducts($financialProducts, [
            ['code' => 'FIN-DEMAT', 'name' => 'Demat Account', 'description' => 'Dematerialized securities account.'],
            ['code' => 'FIN-TRADING', 'name' => 'Trading Account', 'description' => 'Online stock trading account.'],
            ['code' => 'FIN-FIXED-DEPOSIT', 'name' => 'Fixed Deposit', 'description' => 'Fixed deposit investment scheme.'],
        ]);
    }

    private function createProducts(ProductCategory $category, array $products): void
    {
        foreach ($products as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']],
                [
                    'name' => $product['name'],
                    'category_id' => $category->id,
                    'description' => $product['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
