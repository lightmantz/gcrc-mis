<?php

namespace Database\Seeders;

use App\Assessments\AssessmentTypeRegistry;
use App\Models\Assessment;
use App\Models\Child;
use App\Models\EmergencyContact;
use App\Models\Guardian;
use App\Models\Referral;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding demo users...');
        $users = $this->seedUsers();

        $this->command->info('Seeding demo staff...');
        $staff = $this->seedStaff($users);

        $this->command->info('Seeding demo guardians...');
        $guardians = $this->seedGuardians();

        $this->command->info('Seeding demo children...');
        $children = $this->seedChildren($guardians);

        $this->command->info('Seeding demo emergency contacts...');
        $this->seedEmergencyContacts($children);

        $this->command->info('Seeding demo referrals...');
        $this->seedReferrals($children, $users);

        $this->command->info('Seeding demo assessments...');
        $this->seedAssessments($children, $staff);

        $this->command->info('Done. Demo data is ready.');
    }

    /* ================================================================
     |  Users
     | ================================================================ */

    private function seedUsers(): array
    {
        $definitions = [
            ['name' => 'Sarah Mwangi',    'email' => 'sarah@demo.gcrc',    'roles' => ['Center Manager/Director']],
            ['name' => 'Dr. James Otieno','email' => 'james@demo.gcrc',    'roles' => ['Clinical/Medical Staff']],
            ['name' => 'Grace Achieng',   'email' => 'grace@demo.gcrc',    'roles' => ['Physiotherapist']],
            ['name' => 'Mary Wanjiku',    'email' => 'mary@demo.gcrc',     'roles' => ['Teacher']],
            ['name' => 'Peter Kamau',     'email' => 'peter@demo.gcrc',    'roles' => ['Social Worker']],
            ['name' => 'Alice Njeri',     'email' => 'alice@demo.gcrc',    'roles' => ['Nurse']],
            ['name' => 'David Omondi',    'email' => 'david@demo.gcrc',    'roles' => ['Reception/Records Officer']],
            ['name' => 'Ruth Adeyemi',    'email' => 'ruth@demo.gcrc',     'roles' => ['Psychologist/Counselor']],
        ];

        $users = [];

        foreach ($definitions as $def) {
            $user = User::firstOrCreate(
                ['email' => $def['email']],
                [
                    'name'              => $def['name'],
                    'password'          => Hash::make('DemoPass!2026'),
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles($def['roles']);
            $users[] = $user;
        }

        return $users;
    }

    /* ================================================================
     |  Staff
     | ================================================================ */

    private function seedStaff(array $users): array
    {
        // Match staff records to users by email
        $definitions = [
            [
                'user_email' => 'sarah@demo.gcrc',
                'first_name' => 'Sarah', 'last_name' => 'Mwangi',
                'gender' => 'female', 'date_of_birth' => '1985-04-12',
                'phone' => '+254700100001', 'email' => 'sarah@demo.gcrc',
                'category' => 'management', 'department' => 'Administration',
                'job_title' => 'Center Director',
                'employment_type' => 'full_time',
                'employment_start_date' => '2020-01-15',
                'salary' => 85000,
            ],
            [
                'user_email' => 'james@demo.gcrc',
                'first_name' => 'James', 'middle_name' => 'Ochieng', 'last_name' => 'Otieno',
                'gender' => 'male', 'date_of_birth' => '1979-08-22',
                'phone' => '+254700100002', 'email' => 'james@demo.gcrc',
                'category' => 'clinical', 'department' => 'Medical',
                'job_title' => 'Consultant Pediatrician',
                'professional_qualifications' => 'MBChB, MMed (Pediatrics)',
                'specialization' => 'Pediatric Neurology',
                'employment_type' => 'full_time',
                'employment_start_date' => '2021-03-01',
                'salary' => 120000,
            ],
            [
                'user_email' => 'grace@demo.gcrc',
                'first_name' => 'Grace', 'last_name' => 'Achieng',
                'gender' => 'female', 'date_of_birth' => '1990-02-14',
                'phone' => '+254700100003', 'email' => 'grace@demo.gcrc',
                'category' => 'therapy', 'department' => 'Rehabilitation',
                'job_title' => 'Senior Physiotherapist',
                'professional_qualifications' => 'BSc Physiotherapy',
                'specialization' => 'Pediatric Physiotherapy',
                'employment_type' => 'full_time',
                'employment_start_date' => '2022-06-15',
                'salary' => 72000,
            ],
            [
                'user_email' => 'mary@demo.gcrc',
                'first_name' => 'Mary', 'last_name' => 'Wanjiku',
                'gender' => 'female', 'date_of_birth' => '1992-11-30',
                'phone' => '+254700100004', 'email' => 'mary@demo.gcrc',
                'category' => 'education', 'department' => 'Education',
                'job_title' => 'Special Education Teacher',
                'professional_qualifications' => 'BEd Special Needs Education',
                'employment_type' => 'full_time',
                'employment_start_date' => '2022-09-01',
                'salary' => 55000,
            ],
            [
                'user_email' => 'peter@demo.gcrc',
                'first_name' => 'Peter', 'last_name' => 'Kamau',
                'gender' => 'male', 'date_of_birth' => '1988-05-19',
                'phone' => '+254700100005', 'email' => 'peter@demo.gcrc',
                'category' => 'admin', 'department' => 'Social Work',
                'job_title' => 'Social Worker',
                'professional_qualifications' => 'BA Social Work',
                'employment_type' => 'full_time',
                'employment_start_date' => '2023-01-10',
                'salary' => 48000,
            ],
            [
                'user_email' => 'alice@demo.gcrc',
                'first_name' => 'Alice', 'last_name' => 'Njeri',
                'gender' => 'female', 'date_of_birth' => '1995-07-08',
                'phone' => '+254700100006', 'email' => 'alice@demo.gcrc',
                'category' => 'clinical', 'department' => 'Nursing',
                'job_title' => 'Staff Nurse',
                'professional_qualifications' => 'Diploma in Nursing',
                'employment_type' => 'full_time',
                'employment_start_date' => '2023-04-01',
                'salary' => 42000,
            ],
            [
                'user_email' => 'david@demo.gcrc',
                'first_name' => 'David', 'last_name' => 'Omondi',
                'gender' => 'male', 'date_of_birth' => '1996-12-25',
                'phone' => '+254700100007', 'email' => 'david@demo.gcrc',
                'category' => 'admin', 'department' => 'Records',
                'job_title' => 'Receptionist / Records Officer',
                'employment_type' => 'full_time',
                'employment_start_date' => '2024-02-01',
                'salary' => 35000,
            ],
            [
                'user_email' => 'ruth@demo.gcrc',
                'first_name' => 'Ruth', 'last_name' => 'Adeyemi',
                'gender' => 'female', 'date_of_birth' => '1986-09-03',
                'phone' => '+254700100008', 'email' => 'ruth@demo.gcrc',
                'category' => 'clinical', 'department' => 'Psychology',
                'job_title' => 'Clinical Psychologist',
                'professional_qualifications' => 'MSc Clinical Psychology',
                'employment_type' => 'part_time',
                'employment_start_date' => '2023-08-15',
                'salary' => 60000,
            ],
        ];

        $staff = [];

        foreach ($definitions as $def) {
            $user = User::where('email', $def['user_email'])->first();

            $staffMember = Staff::firstOrCreate(
                ['phone' => $def['phone']],
                array_merge(
                    collect($def)->except(['user_email'])->toArray(),
                    ['status' => 'active']
                )
            );

            if ($user && ! $staffMember->users()->where('user_id', $user->id)->exists()) {
                $staffMember->users()->attach($user->id);
            }

            $staff[] = $staffMember;
        }

        return $staff;
    }

    /* ================================================================
     |  Guardians
     | ================================================================ */

    private function seedGuardians(): array
    {
        $definitions = [
            [
                'first_name' => 'Margaret', 'last_name' => 'Njoroge',
                'gender' => 'female', 'date_of_birth' => '1985-03-15',
                'phone' => '+254710200001', 'email' => 'margaret@demo.gcrc',
                'occupation' => 'Trader', 'address' => 'Ngara, Nairobi',
                'district' => 'Nairobi', 'region' => 'Nairobi',
            ],
            [
                'first_name' => 'Joseph', 'last_name' => 'Njoroge',
                'gender' => 'male', 'date_of_birth' => '1982-07-22',
                'phone' => '+254710200002', 'email' => 'joseph@demo.gcrc',
                'occupation' => 'Driver', 'address' => 'Ngara, Nairobi',
                'district' => 'Nairobi', 'region' => 'Nairobi',
            ],
            [
                'first_name' => 'Catherine', 'last_name' => 'Akinyi',
                'gender' => 'female', 'date_of_birth' => '1990-11-08',
                'phone' => '+254710200003', 'email' => 'catherine@demo.gcrc',
                'occupation' => 'Teacher', 'address' => 'Kibera, Nairobi',
                'district' => 'Nairobi', 'region' => 'Nairobi',
            ],
            [
                'first_name' => 'Samuel', 'last_name' => 'Kipchoge',
                'gender' => 'male', 'date_of_birth' => '1978-04-20',
                'phone' => '+254710200004', 'email' => 'samuel@demo.gcrc',
                'occupation' => 'Farmer', 'address' => 'Eldoret',
                'district' => 'Uasin Gishu', 'region' => 'Rift Valley',
            ],
            [
                'first_name' => 'Esther', 'last_name' => 'Wafula',
                'gender' => 'female', 'date_of_birth' => '1993-12-12',
                'phone' => '+254710200005', 'email' => 'esther@demo.gcrc',
                'occupation' => 'Nurse', 'address' => 'Kakamega',
                'district' => 'Kakamega', 'region' => 'Western',
            ],
        ];

        $guardians = [];

        foreach ($definitions as $def) {
            $guardians[] = Guardian::firstOrCreate(
                ['phone' => $def['phone']],
                $def
            );
        }

        return $guardians;
    }

    /* ================================================================
     |  Children
     | ================================================================ */

    private function seedChildren(array $guardians): array
    {
        $definitions = [
            [
                'first_name' => 'Brian', 'last_name' => 'Njoroge',
                'date_of_birth' => '2018-05-20', 'gender' => 'male',
                'primary_condition' => 'cerebral_palsy',
                'disability_summary' => 'Spastic diplegia, delayed milestones, requires physiotherapy.',
                'blood_type' => 'O+', 'phone' => '+254710200001',
                'district' => 'Nairobi', 'region' => 'Nairobi',
                'guardians' => [0, 1],
            ],
            [
                'first_name' => 'Faith', 'last_name' => 'Akinyi',
                'date_of_birth' => '2020-09-12', 'gender' => 'female',
                'primary_condition' => 'down_syndrome',
                'disability_summary' => 'Trisomy 21, developmental delay, requires speech therapy.',
                'blood_type' => 'A+', 'phone' => '+254710200003',
                'district' => 'Nairobi', 'region' => 'Nairobi',
                'guardians' => [2],
            ],
            [
                'first_name' => 'Daniel', 'last_name' => 'Kipchoge',
                'preferred_name' => 'Dan',
                'date_of_birth' => '2017-02-14', 'gender' => 'male',
                'primary_condition' => 'autism_spectrum',
                'disability_summary' => 'ASD level 2, minimal verbal communication, sensory sensitivities.',
                'blood_type' => 'B+', 'phone' => '+254710200004',
                'district' => 'Uasin Gishu', 'region' => 'Rift Valley',
                'guardians' => [3],
                'requires_constant_supervision' => true,
                'special_care_requirements' => 'Sensory-friendly environment, predictable routines.',
                'communication_notes' => 'Responds to picture boards and simple sign language.',
            ],
            [
                'first_name' => 'Aisha', 'last_name' => 'Wafula',
                'date_of_birth' => '2019-11-05', 'gender' => 'female',
                'primary_condition' => 'hearing_impairment',
                'disability_summary' => 'Bilateral sensorineural hearing loss, uses hearing aids.',
                'blood_type' => 'AB+', 'phone' => '+254710200005',
                'district' => 'Kakamega', 'region' => 'Western',
                'guardians' => [4],
                'communication_notes' => 'Learns Kenyan Sign Language, progressing well.',
            ],
            [
                'first_name' => 'Kevin', 'last_name' => 'Mwangi',
                'date_of_birth' => '2016-08-30', 'gender' => 'male',
                'primary_condition' => 'intellectual_disability',
                'disability_summary' => 'Moderate intellectual disability, attends special education unit.',
                'blood_type' => 'O-', 'phone' => '+254710200001',
                'district' => 'Nairobi', 'region' => 'Nairobi',
                'guardians' => [0],
            ],
            [
                'first_name' => 'Naomi', 'last_name' => 'Kipchoge',
                'date_of_birth' => '2021-03-22', 'gender' => 'female',
                'primary_condition' => 'speech_language_disorder',
                'disability_summary' => 'Expressive language delay, no underlying medical cause identified.',
                'blood_type' => 'A-', 'phone' => '+254710200004',
                'district' => 'Uasin Gishu', 'region' => 'Rift Valley',
                'guardians' => [3],
            ],
            [
                'first_name' => 'Michael', 'last_name' => 'Otieno',
                'date_of_birth' => '2015-12-10', 'gender' => 'male',
                'primary_condition' => 'physical_disability',
                'disability_summary' => 'Lower limb amputation following accident, uses prosthetic.',
                'blood_type' => 'B-', 'phone' => '+254710200003',
                'district' => 'Nairobi', 'region' => 'Nairobi',
                'guardians' => [2],
            ],
            [
                'first_name' => 'Grace', 'last_name' => 'Akinyi',
                'date_of_birth' => '2019-06-18', 'gender' => 'female',
                'primary_condition' => 'multiple_disabilities',
                'disability_summary' => 'Cerebral palsy with associated visual impairment.',
                'blood_type' => 'O+', 'phone' => '+254710200003',
                'district' => 'Nairobi', 'region' => 'Nairobi',
                'guardians' => [2],
                'requires_constant_supervision' => true,
            ],
        ];

        $children = [];

        foreach ($definitions as $index => $def) {
            $guardianIndexes = $def['guardians'] ?? [];
            unset($def['guardians']);

            // Every child has a slightly different registration date
            $def['registration_date'] = now()->subDays(30 * ($index + 1))->toDateString();
            $def['status'] = 'active';

            // Reuse an existing child if they were already seeded
            $child = Child::firstOrCreate(
                ['first_name' => $def['first_name'], 'last_name' => $def['last_name'], 'date_of_birth' => $def['date_of_birth']],
                $def
            );

            // Attach guardians with varied relationship metadata
            foreach ($guardianIndexes as $position => $guardianIndex) {
                $guardian = $guardians[$guardianIndex];

                if (! $child->guardians()->where('guardian_id', $guardian->id)->exists()) {
                    $child->guardians()->attach($guardian->id, [
                        'relationship'        => $position === 0 ? ($guardian->gender === 'female' ? 'mother' : 'father') : 'aunt',
                        'is_primary'          => $position === 0,
                        'is_legal'            => $position === 0,
                        'consent_medical'     => $position === 0,
                        'consent_education'   => $position === 0,
                        'consent_photography' => false,
                        'lives_with_child'    => $position === 0,
                    ]);
                }
            }

            $children[] = $child;
        }

        return $children;
    }

    /* ================================================================
     |  Emergency Contacts
     | ================================================================ */

    private function seedEmergencyContacts(array $children): void
    {
        $names = ['Anne Wambui', 'Eric Mwangi', 'Joyce Adhiambo', 'Paul Kiprop', 'Rebecca Nekesa'];
        $relations = ['Neighbor', 'Aunt', 'Family friend', 'Uncle', 'Grandmother'];

        foreach ($children as $index => $child) {
            $count = ($index % 2) + 1; // 1 or 2 contacts per child

            for ($i = 0; $i < $count; $i++) {
                $slot = ($index + $i) % count($names);

                EmergencyContact::firstOrCreate(
                    [
                        'child_id' => $child->id,
                        'name' => $names[$slot],
                    ],
                    [
                        'relationship' => $relations[$slot],
                        'phone'        => '+2547203000' . str_pad((string) ($index * 10 + $i + 1), 2, '0', STR_PAD_LEFT),
                        'priority'     => $i + 1,
                    ]
                );
            }
        }
    }

    /* ================================================================
     |  Referrals
     | ================================================================ */

    private function seedReferrals(array $children, array $users): void
    {
        $sources = [
            ['source_type' => 'hospital',  'source_name' => 'Kenyatta National Hospital', 'source_contact' => '+2540202726300'],
            ['source_type' => 'clinic',    'source_name' => 'Mama Lucy Kibaki Hospital',  'source_contact' => '+2540203560000'],
            ['source_type' => 'community', 'source_name' => 'Community Health Volunteer — Kibera', 'source_contact' => null],
            ['source_type' => 'school',    'source_name' => 'Nairobi Special School',      'source_contact' => '+2540205551234'],
            ['source_type' => 'walk_in',   'source_name' => null,                          'source_contact' => null],
        ];

        $reasons = [
            'Referred for physiotherapy following delayed motor milestones.',
            'Parent sought assessment for speech delay, referred by local clinic.',
            'Referred for comprehensive assessment and possible enrollment.',
            'Teacher at school noted learning difficulties and recommended assessment.',
            'Family self-referred after hearing about the center from a neighbor.',
            'Follow-up referral for ongoing therapy following initial assessment.',
        ];

        $statuses = ['received', 'accepted', 'in_progress', 'completed'];

        $adminUser = collect($users)->firstWhere('email', 'david@demo.gcrc')
            ?? $users[0];

        foreach ($children as $index => $child) {
            // Not every child has a referral — but most do
            if ($index % 4 === 3) {
                continue;
            }

            $source = $sources[$index % count($sources)];
            $status = $statuses[$index % count($statuses)];

            Referral::firstOrCreate(
                [
                    'child_id' => $child->id,
                    'referral_date' => $child->registration_date,
                ],
                [
                    'source_type'    => $source['source_type'],
                    'source_name'    => $source['source_name'],
                    'source_contact' => $source['source_contact'],
                    'reason'         => $reasons[$index % count($reasons)],
                    'status'         => $status,
                    'registered_by'  => $adminUser->id,
                ]
            );
        }
    }

    /* ================================================================
     |  Assessments
     | ================================================================ */

    private function seedAssessments(array $children, array $staff): void
    {
        // Map staff categories for assessor lookup
        $clinicalStaff = collect($staff)->where('category', 'clinical')->values();
        $therapyStaff  = collect($staff)->where('category', 'therapy')->values();
        $adminStaff    = collect($staff)->where('category', 'admin')->values();

        $assessorFor = function (string $type) use ($clinicalStaff, $therapyStaff, $adminStaff) {
            $typeInstance = AssessmentTypeRegistry::get($type);
            $category = $typeInstance?->assessorCategory();

            return match ($category) {
                'clinical' => $clinicalStaff->first(),
                'therapy'  => $therapyStaff->first(),
                'admin'    => $adminStaff->first(),
                default    => $clinicalStaff->first(),
            };
        };

        $medicalFindings = [
            'general_condition'    => 'good',
            'nutritional_status'   => 'normal',
            'height_cm'            => 105,
            'weight_kg'            => 18.5,
            'consciousness_level'  => 'alert',
            'seizure_history'      => 'none',
            'immunization_status'  => 'up_to_date',
            'presenting_complaints' => 'Referred for comprehensive pediatric assessment.',
            'medical_observations' => 'No acute concerns. Vitals within normal limits for age.',
        ];

        $physioFindings = [
            'head_control'         => 'independent',
            'sitting_balance'      => 'independent',
            'standing_balance'     => 'with_support',
            'walking'              => 'with_assistance',
            'hand_function_right'  => 'functional',
            'hand_function_left'   => 'partial',
            'muscle_tone'          => 'hypertonic',
            'range_of_motion'      => 'mild_restriction',
            'clinical_impression'  => 'Spastic diplegia with mild lower-limb involvement. Good potential for functional gains with regular therapy.',
            'treatment_goals'      => 'Improve standing balance, work toward independent walking with minimal support.',
            'recommended_frequency' => 'twice_weekly',
        ];

        $speechFindings = [
            'comprehension'        => 'mild_delay',
            'follows_instructions' => 'two_step',
            'vocabulary'           => 'limited',
            'sentence_formation'   => 'phrases',
            'articulation'         => 'mild_errors',
            'fluency'              => 'fluent',
            'voice_quality'        => 'normal',
            'primary_mode'         => 'verbal',
            'aac_recommended'      => 'no',
            'clinical_impression'  => 'Expressive language delay with age-appropriate comprehension. Articulation errors expected to resolve with therapy.',
            'therapy_goals'        => 'Expand vocabulary to 100+ words, form 3-word sentences.',
            'recommended_frequency' => 'twice_weekly',
        ];

        $otFindings = [
            'feeding'              => 'independent',
            'dressing'             => 'supervision',
            'toileting'            => 'supervision',
            'personal_hygiene'     => 'assistance',
            'pincer_grasp'         => 'present',
            'handwriting'          => 'emerging',
            'bilateral_coordination' => 'fair',
            'tactile_response'     => 'typical',
            'auditory_response'    => 'typical',
            'vestibular_response'  => 'typical',
            'clinical_impression'  => 'Developing age-appropriate self-care skills. Fine motor delays consistent with overall developmental profile.',
            'functional_goals'     => 'Independent dressing, age-appropriate handwriting.',
            'recommended_equipment' => 'Adaptive pencil grips, dressing aids.',
        ];

        $psychFindings = [
            'primary_concern'      => 'Parent reports concerns about behavior and social interaction.',
            'concern_duration'     => '6_to_12_months',
            'attention'            => 'mild_difficulty',
            'memory'               => 'age_appropriate',
            'problem_solving'      => 'delayed',
            'mood'                 => 'euthymic',
            'affect'               => 'appropriate',
            'social_interaction'   => 'withdrawn',
            'family_support'       => 'moderate',
            'clinical_impression'  => 'Adjustment difficulties with emerging social withdrawal. No acute psychopathology identified.',
            'recommendations'      => 'Regular counseling sessions, family psychoeducation.',
            'referral_needed'      => 'no',
        ];

        $socialFindings = [
            'household_size'               => 5,
            'primary_caregiver'            => 'Mother',
            'caregiver_relationship'       => 'Biological mother',
            'housing_type'                 => 'rented',
            'housing_condition'            => 'adequate',
            'utilities'                    => 'full',
            'distance_to_center'           => '5_to_15km',
            'income_source'                => 'Informal trading',
            'financial_stability'          => 'variable',
            'family_support_level'         => 'moderate',
            'child_protection_concerns'    => 'none',
            'social_worker_impression'     => 'Family is engaged and supportive. Primary barrier to attendance is transport cost.',
            'intervention_plan'            => 'Refer to transport assistance program. Monthly home visit.',
            'referral_needed'              => 'no',
        ];

        $functionalFindings = [
            'transfers'              => 'independent',
            'community_mobility'     => 'with_device',
            'stairs'                 => 'with_rail',
            'bathing'                => 'supervision',
            'dressing'               => 'assistance',
            'eating'                 => 'independent',
            'continence'             => 'occasional_accidents',
            'expresses_needs'        => 'verbally',
            'follows_routines'       => 'with_prompts',
            'safety_awareness'       => 'reduced',
            'school_participation'   => 'partial',
            'social_participation'   => 'limited',
            'summary_of_function'    => 'Overall functional independence is age-appropriate with support. Priority areas: mobility and self-care.',
            'priority_needs'         => 'Safe mobility in community, independence in dressing.',
            'intervention_priorities' => 'Physiotherapy for gait training, OT for dressing skills.',
        ];

        $typeFindings = [
            'medical'              => $medicalFindings,
            'physiotherapy'        => $physioFindings,
            'occupational_therapy' => $otFindings,
            'speech'               => $speechFindings,
            'psychological'        => $psychFindings,
            'social'               => $socialFindings,
            'functional'           => $functionalFindings,
        ];

        $availableTypes = AssessmentTypeRegistry::keys();

        foreach ($children as $childIndex => $child) {
            // Every child gets a medical assessment
            $this->createAssessment(
                $child,
                'medical',
                $medicalFindings,
                $assessorFor('medical'),
                $child->registration_date,
                'finalized'
            );

            // Some children get additional assessments
            if ($childIndex % 2 === 0) {
                $this->createAssessment(
                    $child,
                    'physiotherapy',
                    $physioFindings,
                    $assessorFor('physiotherapy'),
                    now()->subDays(20)->toDateString(),
                    'finalized'
                );
            }

            if ($childIndex % 3 === 0) {
                $this->createAssessment(
                    $child,
                    'speech',
                    $speechFindings,
                    $assessorFor('speech'),
                    now()->subDays(10)->toDateString(),
                    $childIndex % 2 === 0 ? 'finalized' : 'draft'
                );
            }

            if ($childIndex % 4 === 0) {
                $this->createAssessment(
                    $child,
                    'social',
                    $socialFindings,
                    $assessorFor('social'),
                    now()->subDays(15)->toDateString(),
                    'finalized'
                );
            }

            if ($childIndex % 5 === 0) {
                $this->createAssessment(
                    $child,
                    'occupational_therapy',
                    $otFindings,
                    $assessorFor('occupational_therapy'),
                    now()->subDays(5)->toDateString(),
                    'draft'
                );
            }
        }
    }

    private function createAssessment(
        Child $child,
        string $type,
        array $findings,
        ?Staff $assessor,
        string $date,
        string $status,
    ): void {
        if (! $assessor) {
            return; // no staff for this category — skip
        }

        // Skip if this exact child/type/date already exists
        $exists = Assessment::where('child_id', $child->id)
            ->where('type', $type)
            ->where('assessment_date', $date)
            ->exists();

        if ($exists) {
            return;
        }

        $finalized = $status === 'finalized';

        Assessment::create([
            'child_id'          => $child->id,
            'assessor_id'       => $assessor->id,
            'type'              => $type,
            'assessment_date'   => $date,
            'status'            => $status,
            'summary'           => 'Initial ' . str_replace('_', ' ', $type) . ' assessment. Findings documented.',
            'recommendations'   => 'Proceed with recommended interventions as outlined in findings.',
            'findings'          => $findings,
            'finalized_at'      => $finalized ? now()->subDays(2) : null,
            'finalized_by'      => $finalized ? User::where('email', 'sarah@demo.gcrc')->first()?->id : null,
        ]);
    }
}