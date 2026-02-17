<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Roles ───
        $roles = [
            ['id' => 1, 'name' => 'Administrator',      'description' => 'Full system access — all permissions automatically granted'],
            ['id' => 2, 'name' => 'Librarian',           'description' => 'Circulation, catalog management, and reporting'],
            ['id' => 3, 'name' => 'Student Assistant',    'description' => 'Issue/Return books and basic circulation'],
            ['id' => 4, 'name' => 'Student',              'description' => 'Self-service portal — view catalog, borrowing history'],
            ['id' => 5, 'name' => 'Faculty',              'description' => 'Faculty portal — view catalog, extended borrowing'],
        ];
        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, ['created_at' => now(), 'updated_at' => now()]));
        }

        // ─── Permissions ───
        $permissions = [
            // Books
            ['name' => 'can_issue_books',      'label' => 'Issue Books',          'group' => 'Books',   'description' => 'Allow issuing books to students and faculty'],
            ['name' => 'can_return_books',      'label' => 'Return Books',         'group' => 'Books',   'description' => 'Allow processing book returns'],
            ['name' => 'can_manage_books',      'label' => 'Manage Books',         'group' => 'Books',   'description' => 'Add, edit, or remove books from the catalog'],

            // Catalog
            ['name' => 'can_add_categories',    'label' => 'Add / Manage Categories', 'group' => 'Catalog', 'description' => 'Create, edit, or delete book categories'],
            ['name' => 'can_manage_authors',    'label' => 'Manage Authors',       'group' => 'Catalog', 'description' => 'Add, edit, or remove author records'],
            ['name' => 'can_manage_publishers', 'label' => 'Manage Publishers',    'group' => 'Catalog', 'description' => 'Add, edit, or remove publisher records'],

            // Reports
            ['name' => 'can_view_reports',      'label' => 'View Reports',         'group' => 'Reports', 'description' => 'Access and generate library reports'],

            // Users
            ['name' => 'can_manage_users',      'label' => 'Manage Users',         'group' => 'Users',   'description' => 'Create, edit, or deactivate user accounts'],
            ['name' => 'can_manage_roles',      'label' => 'Manage Roles & Permissions', 'group' => 'Users', 'description' => 'Assign and modify role permissions'],
            ['name' => 'can_manage_students',   'label' => 'Manage Students',      'group' => 'Users',   'description' => 'Add, edit, or remove student records'],

            // System
            ['name' => 'can_manage_settings',   'label' => 'Manage Settings',      'group' => 'System',  'description' => 'Configure library system settings'],
            ['name' => 'can_manage_backups',    'label' => 'Manage Backups',       'group' => 'System',  'description' => 'Create and restore database backups'],
            ['name' => 'can_view_audit_logs',   'label' => 'View Audit Logs',      'group' => 'System',  'description' => 'View system activity and audit trail'],
        ];
        foreach ($permissions as $perm) {
            DB::table('permissions')->insert(array_merge($perm, ['created_at' => now(), 'updated_at' => now()]));
        }

        // ─── Role-Permission Assignments ───
        // Administrator (role_id=1) — gets ALL automatically via Gate, no pivot rows needed
        // Librarian (role_id=2) — books, catalog, reports, students
        $librarianPerms = [1, 2, 3, 4, 5, 6, 7, 10]; // can_issue, return, manage_books, categories, authors, publishers, reports, students
        foreach ($librarianPerms as $permId) {
            DB::table('role_permission')->insert(['role_id' => 2, 'permission_id' => $permId]);
        }

        // Student Assistant (role_id=3) — issue/return only
        foreach ([1, 2] as $permId) {
            DB::table('role_permission')->insert(['role_id' => 3, 'permission_id' => $permId]);
        }

        // Student (role_id=4) — no special permissions (view-only)
        // Faculty (role_id=5) — no special permissions (view-only)

        // ─── Admin User ───
        DB::table('users')->insert([
            'role_id'    => 1,
            'user_type'  => 'librarian',
            'username'   => 'admin',
            'name'       => 'System Administrator',
            'email'      => 'admin@library.com',
            'password'   => Hash::make('admin123'),
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ─── Librarian User ───
        DB::table('users')->insert([
            'role_id'    => 2,
            'user_type'  => 'librarian',
            'username'   => 'librarian',
            'name'       => 'Maria Santos',
            'email'      => 'librarian@library.com',
            'password'   => Hash::make('librarian123'),
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ─── Student Assistant User ───
        DB::table('users')->insert([
            'role_id'    => 3,
            'user_type'  => 'student_assistant',
            'username'   => 'assistant',
            'name'       => 'Pedro Reyes',
            'email'      => 'assistant@library.com',
            'password'   => Hash::make('assistant123'),
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ─── Faculty User (Teacher) ───
        DB::table('users')->insert([
            'role_id'         => 5,
            'user_type'       => 'faculty',
            'faculty_subtype' => 'teacher',
            'username'        => 'teacher',
            'name'            => 'Dr. Elena Cruz',
            'email'           => 'teacher@library.com',
            'password'        => Hash::make('teacher123'),
            'status'          => 'active',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ─── Faculty User (Staff) ───
        DB::table('users')->insert([
            'role_id'         => 5,
            'user_type'       => 'faculty',
            'faculty_subtype' => 'staff',
            'username'        => 'staff',
            'name'            => 'Rosa Mendez',
            'email'           => 'staff@library.com',
            'password'        => Hash::make('staff123'),
            'status'          => 'active',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ─── Academic Year ───
        DB::table('academic_years')->insert([
            'name'       => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date'   => '2026-03-31',
            'is_current' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ─── Grade Levels ───
        for ($i = 1; $i <= 12; $i++) {
            DB::table('grade_levels')->insert([
                'name'        => "Grade $i",
                'level_order' => $i,
                'status'      => 'active',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ─── Sections ───
        $sectionNames = ['A', 'B', 'C'];
        for ($g = 1; $g <= 12; $g++) {
            foreach ($sectionNames as $s) {
                DB::table('sections')->insert([
                    'grade_level_id' => $g,
                    'name'           => "Section $s",
                    'status'         => 'active',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        // ─── LCC Categories (with sub-categories for demo) ───
        $categories = [
            ['name' => 'A - General Works',               'description' => 'Encyclopedias, dictionaries, general reference works'],
            ['name' => 'B - Philosophy, Psychology, Religion', 'description' => 'Philosophy, psychology, and religion'],
            ['name' => 'C - Auxiliary Sciences of History', 'description' => 'Archaeology, genealogy, biography as a discipline'],
            ['name' => 'D - World History',                'description' => 'General and Old World history'],
            ['name' => 'E - History of the Americas',      'description' => 'United States and Americas history'],
            ['name' => 'F - History of the Americas (Local)', 'description' => 'Local history of the Americas, Latin America'],
            ['name' => 'G - Geography, Anthropology, Recreation', 'description' => 'Geography, maps, anthropology, sports'],
            ['name' => 'H - Social Sciences',              'description' => 'Economics, sociology, commerce, finance'],
            ['name' => 'J - Political Science',            'description' => 'Political science, international law, public administration'],
            ['name' => 'K - Law',                          'description' => 'Law of specific jurisdictions and general law'],
            ['name' => 'L - Education',                    'description' => 'Education, pedagogy, teaching methods'],
            ['name' => 'M - Music',                        'description' => 'Music, books on music, musical instruction'],
            ['name' => 'N - Fine Arts',                    'description' => 'Visual arts, architecture, sculpture, painting'],
            ['name' => 'P - Language and Literature',      'description' => 'Philology, linguistics, literature, literary history'],
            ['name' => 'Q - Science',                      'description' => 'Mathematics, astronomy, physics, chemistry, biology'],
            ['name' => 'R - Medicine',                     'description' => 'Medicine, nursing, dentistry, pharmacy'],
            ['name' => 'S - Agriculture',                  'description' => 'Agriculture, plant and animal culture, forestry'],
            ['name' => 'T - Technology',                   'description' => 'Engineering, manufacturing, home economics, computer science'],
            ['name' => 'U - Military Science',             'description' => 'Military science, army, navy, air force'],
            ['name' => 'Z - Bibliography, Library Science', 'description' => 'Bibliography, library science, information resources'],
        ];
        foreach ($categories as $cat) {
            DB::table('categories')->insert(array_merge($cat, [
                'parent_id'  => null,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ─── Demo Sub-Categories (to showcase recursive navigation) ───
        // Sub-categories under "Q - Science" (id=15)
        $scienceSubCats = [
            ['parent_id' => 15, 'name' => 'QA - Mathematics',   'description' => 'Mathematics, algebra, calculus'],
            ['parent_id' => 15, 'name' => 'QB - Astronomy',     'description' => 'Astronomy and astrophysics'],
            ['parent_id' => 15, 'name' => 'QC - Physics',       'description' => 'Physics and physical sciences'],
            ['parent_id' => 15, 'name' => 'QD - Chemistry',     'description' => 'Chemistry and chemical sciences'],
            ['parent_id' => 15, 'name' => 'QH - Biology',       'description' => 'Biology, natural history, ecology'],
        ];
        foreach ($scienceSubCats as $sub) {
            DB::table('categories')->insert(array_merge($sub, [
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Sub-categories under "T - Technology" (id=18)
        $techSubCats = [
            ['parent_id' => 18, 'name' => 'TA - Engineering',       'description' => 'General engineering'],
            ['parent_id' => 18, 'name' => 'TK - Electrical Engr.',  'description' => 'Electrical and electronic engineering'],
            ['parent_id' => 18, 'name' => 'QA76 - Computer Science', 'description' => 'Computer science and programming'],
        ];
        foreach ($techSubCats as $sub) {
            DB::table('categories')->insert(array_merge($sub, [
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ─── Authors ───
        $authors = [
            ['name' => 'Encyclopaedia Britannica Editors', 'nationality' => 'British'],
            ['name' => 'Plato',               'nationality' => 'Greek'],
            ['name' => 'Daniel Kahneman',      'nationality' => 'Israeli-American'],
            ['name' => 'C.S. Lewis',           'nationality' => 'British'],
            ['name' => 'Howard Zinn',          'nationality' => 'American'],
            ['name' => 'Ambrosio Rianzares Bautista', 'nationality' => 'Filipino'],
            ['name' => 'Jared Diamond',        'nationality' => 'American'],
            ['name' => 'Adam Smith',           'nationality' => 'Scottish'],
            ['name' => 'Thomas Piketty',       'nationality' => 'French'],
            ['name' => 'John Rawls',           'nationality' => 'American'],
            ['name' => 'Miriam Defensor Santiago', 'nationality' => 'Filipino'],
            ['name' => 'Paulo Freire',         'nationality' => 'Brazilian'],
            ['name' => 'E.H. Gombrich',        'nationality' => 'Austrian-British'],
            ['name' => 'Jose Rizal',           'nationality' => 'Filipino'],
            ['name' => 'William Shakespeare',  'nationality' => 'British'],
            ['name' => 'Harper Lee',           'nationality' => 'American'],
            ['name' => 'F. Sionil Jose',       'nationality' => 'Filipino'],
            ['name' => 'Nick Joaquin',         'nationality' => 'Filipino'],
            ['name' => 'Charles Darwin',       'nationality' => 'British'],
            ['name' => 'Stephen Hawking',      'nationality' => 'British'],
            ['name' => 'James Stewart',        'nationality' => 'Canadian'],
            ['name' => 'Serway & Jewett',      'nationality' => 'American'],
            ['name' => 'Henry Gray',           'nationality' => 'British'],
            ['name' => 'Harrison (Kasper et al.)', 'nationality' => 'American'],
            ['name' => 'Rachel Carson',        'nationality' => 'American'],
            ['name' => 'Robert C. Martin',     'nationality' => 'American'],
            ['name' => 'Andrew S. Tanenbaum',  'nationality' => 'American'],
            ['name' => 'Thomas H. Cormen',     'nationality' => 'American'],
            ['name' => 'Sun Tzu',              'nationality' => 'Chinese'],
            ['name' => 'Lois Mai Chan',        'nationality' => 'American'],
        ];
        foreach ($authors as $author) {
            DB::table('authors')->insert(array_merge($author, ['created_at' => now(), 'updated_at' => now()]));
        }

        // ─── Publishers ───
        $publishers = [
            ['name' => 'Encyclopaedia Britannica, Inc.', 'location' => 'Chicago, IL, USA',     'contact' => '+1-312-347-7000'],
            ['name' => 'Hackett Publishing',             'location' => 'Indianapolis, IN, USA', 'contact' => '+1-317-635-9250'],
            ['name' => 'Farrar, Straus and Giroux',      'location' => 'New York, NY, USA',     'contact' => '+1-212-741-6900'],
            ['name' => 'HarperCollins',                  'location' => 'New York, NY, USA',     'contact' => '+1-212-207-7000'],
            ['name' => 'Harper Perennial',               'location' => 'New York, NY, USA',     'contact' => '+1-212-207-7000'],
            ['name' => 'W.W. Norton & Company',          'location' => 'New York, NY, USA',     'contact' => '+1-212-354-5500'],
            ['name' => 'Penguin Books',                  'location' => 'London, UK',            'contact' => '+44-20-7139-3000'],
            ['name' => 'Harvard University Press',       'location' => 'Cambridge, MA, USA',    'contact' => '+1-617-495-2600'],
            ['name' => 'Bloomsbury Publishing',          'location' => 'London, UK',            'contact' => '+44-20-7631-5600'],
            ['name' => 'Phaidon Press',                  'location' => 'London, UK',            'contact' => '+44-20-7843-1000'],
            ['name' => 'Penguin Classics',               'location' => 'London, UK',            'contact' => '+44-20-7139-3000'],
            ['name' => 'Solidaridad Publishing House',   'location' => 'Manila, Philippines',   'contact' => '+63-2-8524-4757'],
            ['name' => 'Anvil Publishing',               'location' => 'Pasig City, Philippines', 'contact' => '+63-2-8477-4752'],
            ['name' => 'Cengage Learning',               'location' => 'Boston, MA, USA',       'contact' => '+1-617-289-7700'],
            ['name' => 'John Wiley & Sons',              'location' => 'Hoboken, NJ, USA',      'contact' => '+1-201-748-6000'],
            ['name' => 'Bantam Books',                   'location' => 'New York, NY, USA',     'contact' => '+1-212-782-9000'],
            ['name' => 'Elsevier',                       'location' => 'Amsterdam, Netherlands', 'contact' => '+31-20-485-3911'],
            ['name' => 'McGraw-Hill',                    'location' => 'New York, NY, USA',     'contact' => '+1-212-904-2000'],
            ['name' => 'Houghton Mifflin Harcourt',      'location' => 'Boston, MA, USA',       'contact' => '+1-617-351-5000'],
            ['name' => 'Pearson Education',              'location' => 'London, UK',            'contact' => '+44-20-7010-2000'],
            ['name' => 'MIT Press',                      'location' => 'Cambridge, MA, USA',    'contact' => '+1-617-253-5646'],
            ['name' => 'Rex Book Store',                 'location' => 'Manila, Philippines',   'contact' => '+63-2-8735-1364'],
            ['name' => 'Libraries Unlimited',            'location' => 'Santa Barbara, CA, USA', 'contact' => '+1-805-968-1911'],
            ['name' => 'Prentice Hall',                  'location' => 'Upper Saddle River, NJ, USA', 'contact' => '+1-201-236-7000'],
        ];
        foreach ($publishers as $pub) {
            DB::table('publishers')->insert(array_merge($pub, ['created_at' => now(), 'updated_at' => now()]));
        }

        // ─── Books (LCC-classified certified titles) ───
        $books = [
            ['isbn' => '978-1593392925', 'title' => 'Encyclopaedia Britannica (Concise Edition)', 'category_id' => 1, 'publication_year' => 2006, 'total_copies' => 3, 'available_copies' => 3, 'shelf_location' => 'A-01-01', 'description' => 'LCC: AE5 .E363 — Concise general knowledge encyclopedia covering all major fields.'],
            ['isbn' => '978-0872201248', 'title' => 'The Republic', 'category_id' => 2, 'publication_year' => 1992, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'B-01-01', 'description' => 'LCC: JC71 .P35 — Classic philosophical dialogue on justice, the ideal state, and the philosopher-king.'],
            ['isbn' => '978-0374533557', 'title' => 'Thinking, Fast and Slow', 'category_id' => 2, 'publication_year' => 2011, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'B-01-02', 'description' => 'LCC: BF441 .K235 — Explores the two systems of thought that drive the way we think and make decisions.'],
            ['isbn' => '978-0060652920', 'title' => 'Mere Christianity', 'category_id' => 2, 'publication_year' => 2001, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'B-01-03', 'description' => 'LCC: BT77 .L48 — Theological exploration of Christian belief compiled from BBC radio talks.'],
            ['isbn' => '978-0060838652', 'title' => 'A People\'s History of the United States', 'category_id' => 5, 'publication_year' => 2015, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'D-01-01', 'description' => 'LCC: E178 .Z56 — American history from the perspective of marginalized peoples.'],
            ['isbn' => '978-0393354324', 'title' => 'Guns, Germs, and Steel: The Fates of Human Societies', 'category_id' => 7, 'publication_year' => 2017, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'G-01-01', 'description' => 'LCC: HM206 .D48 — Explores why certain civilizations became dominant through geography, agriculture, and technology.'],
            ['isbn' => '978-0140432084', 'title' => 'The Wealth of Nations', 'category_id' => 8, 'publication_year' => 1999, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'H-01-01', 'description' => 'LCC: HB161 .S6 — Foundational work in economics discussing free markets, division of labor, and trade.'],
            ['isbn' => '978-0674979857', 'title' => 'Capital in the Twenty-First Century', 'category_id' => 8, 'publication_year' => 2014, 'total_copies' => 3, 'available_copies' => 3, 'shelf_location' => 'H-01-02', 'description' => 'LCC: HB501 .P43613 — Analyzes wealth concentration and distribution over the past 250 years.'],
            ['isbn' => '978-0674000780', 'title' => 'A Theory of Justice', 'category_id' => 9, 'publication_year' => 1999, 'total_copies' => 3, 'available_copies' => 3, 'shelf_location' => 'J-01-01', 'description' => 'LCC: JC578 .R38 — Landmark work on distributive justice and the social contract.'],
            ['isbn' => '978-9710873012', 'title' => 'Philippine Political Law', 'category_id' => 10, 'publication_year' => 2011, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'K-01-01', 'description' => 'LCC: KPM2070 .S26 — Comprehensive text on the Philippine Constitution and political law.'],
            ['isbn' => '978-0826412768', 'title' => 'Pedagogy of the Oppressed', 'category_id' => 11, 'publication_year' => 2000, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'L-01-01', 'description' => 'LCC: LB880 .F73 — Foundational text on critical pedagogy and education as a practice of freedom.'],
            ['isbn' => '978-0714832470', 'title' => 'The Story of Art', 'category_id' => 13, 'publication_year' => 1995, 'total_copies' => 3, 'available_copies' => 3, 'shelf_location' => 'N-01-01', 'description' => 'LCC: N5300 .G6 — One of the most famous and popular books on art ever written, covering prehistoric to modern art.'],
            ['isbn' => '978-9710872985', 'title' => 'Noli Me Tangere', 'category_id' => 14, 'publication_year' => 1887, 'total_copies' => 12, 'available_copies' => 12, 'shelf_location' => 'P-01-01', 'description' => 'LCC: PQ8897 .R5 N6 — Jose Rizal\'s novel exposing the injustices of Spanish colonial rule in the Philippines.'],
            ['isbn' => '978-9710872992', 'title' => 'El Filibusterismo', 'category_id' => 14, 'publication_year' => 1891, 'total_copies' => 12, 'available_copies' => 12, 'shelf_location' => 'P-01-02', 'description' => 'LCC: PQ8897 .R5 E4 — Sequel to Noli Me Tangere; portrays the dark side of colonial society and revolution.'],
            ['isbn' => '978-0743477109', 'title' => 'Romeo and Juliet', 'category_id' => 14, 'publication_year' => 2004, 'total_copies' => 8, 'available_copies' => 8, 'shelf_location' => 'P-01-03', 'description' => 'LCC: PR2831 .A2 — Shakespeare\'s famous tragedy of star-crossed lovers from rival families.'],
            ['isbn' => '978-0743477116', 'title' => 'Hamlet', 'category_id' => 14, 'publication_year' => 2003, 'total_copies' => 6, 'available_copies' => 6, 'shelf_location' => 'P-01-04', 'description' => 'LCC: PR2807 .A2 — Shakespeare\'s masterpiece on revenge, betrayal, and moral corruption in the Danish court.'],
            ['isbn' => '978-0060935467', 'title' => 'To Kill a Mockingbird', 'category_id' => 14, 'publication_year' => 1960, 'total_copies' => 8, 'available_copies' => 8, 'shelf_location' => 'P-01-05', 'description' => 'LCC: PS3562.E353 T6 — A novel about racial injustice in the American South, seen through a child\'s eyes.'],
            ['isbn' => '978-9710540143', 'title' => 'The Pretenders', 'category_id' => 14, 'publication_year' => 1962, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'P-01-06', 'description' => 'LCC: PR9550.9 .J67 P7 — First novel of the Rosales Saga following an Ilokano family through Philippine history.'],
            ['isbn' => '978-9710543088', 'title' => 'The Woman Who Had Two Navels', 'category_id' => 14, 'publication_year' => 1961, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'P-01-07', 'description' => 'LCC: PR9550.9 .J6 W6 — Nick Joaquin\'s novel exploring Filipino identity, myth, and colonial legacy.'],
            ['isbn' => '978-0451529060', 'title' => 'On the Origin of Species', 'category_id' => 15, 'publication_year' => 2003, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'Q-01-01', 'description' => 'LCC: QH365 .O2 — Darwin\'s foundational work on evolution through natural selection.'],
            ['isbn' => '978-0553380163', 'title' => 'A Brief History of Time', 'category_id' => 15, 'publication_year' => 1998, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'Q-01-02', 'description' => 'LCC: QB981 .H377 — Explores cosmology, black holes, and the nature of time for general readers.'],
            ['isbn' => '978-1285740621', 'title' => 'Calculus: Early Transcendentals', 'category_id' => 15, 'publication_year' => 2015, 'total_copies' => 10, 'available_copies' => 10, 'shelf_location' => 'Q-02-01', 'description' => 'LCC: QA303.2 .S73 — Widely used university textbook covering single and multivariable calculus.'],
            ['isbn' => '978-1133947271', 'title' => 'Physics for Scientists and Engineers', 'category_id' => 15, 'publication_year' => 2013, 'total_copies' => 6, 'available_copies' => 6, 'shelf_location' => 'Q-02-02', 'description' => 'LCC: QC21.3 .S47 — Comprehensive physics textbook covering mechanics, thermodynamics, electromagnetism, and modern physics.'],
            ['isbn' => '978-0702052309', 'title' => 'Gray\'s Anatomy (42nd Edition)', 'category_id' => 16, 'publication_year' => 2020, 'total_copies' => 3, 'available_copies' => 3, 'shelf_location' => 'R-01-01', 'description' => 'LCC: QM23.2 .G73 — The definitive anatomical reference used by medical students and professionals worldwide.'],
            ['isbn' => '978-1259644030', 'title' => 'Harrison\'s Principles of Internal Medicine (20th Edition)', 'category_id' => 16, 'publication_year' => 2018, 'total_copies' => 3, 'available_copies' => 3, 'shelf_location' => 'R-01-02', 'description' => 'LCC: RC46 .H33 — Gold-standard internal medicine reference covering diagnosis and treatment of disease.'],
            ['isbn' => '978-0618249060', 'title' => 'Silent Spring', 'category_id' => 17, 'publication_year' => 2002, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'S-01-01', 'description' => 'LCC: QH545.P4 C37 — Landmark book on the environmental impact of pesticides that launched the modern environmental movement.'],
            ['isbn' => '978-0132350884', 'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship', 'category_id' => 18, 'publication_year' => 2008, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'T-01-01', 'description' => 'LCC: QA76.76.D47 M367 — Guide to writing readable, maintainable, and elegant software code.'],
            ['isbn' => '978-0132126953', 'title' => 'Modern Operating Systems (4th Edition)', 'category_id' => 18, 'publication_year' => 2014, 'total_copies' => 4, 'available_copies' => 4, 'shelf_location' => 'T-01-02', 'description' => 'LCC: QA76.76.O63 T36 — Comprehensive textbook on OS design: processes, memory, file systems, and security.'],
            ['isbn' => '978-0262033848', 'title' => 'Introduction to Algorithms (3rd Edition)', 'category_id' => 18, 'publication_year' => 2009, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'T-01-03', 'description' => 'LCC: QA76.6 .C662 — Definitive textbook on algorithms covering sorting, graph theory, dynamic programming, and more.'],
            ['isbn' => '978-1590302255', 'title' => 'The Art of War', 'category_id' => 19, 'publication_year' => 2003, 'total_copies' => 5, 'available_copies' => 5, 'shelf_location' => 'U-01-01', 'description' => 'LCC: U101 .S95 — Ancient Chinese military treatise on strategy, leadership, and tactics.'],
            ['isbn' => '978-1591581543', 'title' => 'Cataloging and Classification: An Introduction', 'category_id' => 20, 'publication_year' => 2006, 'total_copies' => 3, 'available_copies' => 3, 'shelf_location' => 'Z-01-01', 'description' => 'LCC: Z693 .C48 — Standard textbook for library cataloging covering MARC, AACR2, LCC, and DDC.'],
        ];

        foreach ($books as $book) {
            DB::table('books')->insert(array_merge($book, [
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ─── Book-Author relationships ───
        $bookAuthors = [
            ['book_id' => 1,  'author_id' => 1],
            ['book_id' => 2,  'author_id' => 2],
            ['book_id' => 3,  'author_id' => 3],
            ['book_id' => 4,  'author_id' => 4],
            ['book_id' => 5,  'author_id' => 5],
            ['book_id' => 6,  'author_id' => 7],
            ['book_id' => 7,  'author_id' => 8],
            ['book_id' => 8,  'author_id' => 9],
            ['book_id' => 9,  'author_id' => 10],
            ['book_id' => 10, 'author_id' => 11],
            ['book_id' => 11, 'author_id' => 12],
            ['book_id' => 12, 'author_id' => 13],
            ['book_id' => 13, 'author_id' => 14],
            ['book_id' => 14, 'author_id' => 14],
            ['book_id' => 15, 'author_id' => 15],
            ['book_id' => 16, 'author_id' => 15],
            ['book_id' => 17, 'author_id' => 16],
            ['book_id' => 18, 'author_id' => 17],
            ['book_id' => 19, 'author_id' => 18],
            ['book_id' => 20, 'author_id' => 19],
            ['book_id' => 21, 'author_id' => 20],
            ['book_id' => 22, 'author_id' => 21],
            ['book_id' => 23, 'author_id' => 22],
            ['book_id' => 24, 'author_id' => 23],
            ['book_id' => 25, 'author_id' => 24],
            ['book_id' => 26, 'author_id' => 25],
            ['book_id' => 27, 'author_id' => 26],
            ['book_id' => 28, 'author_id' => 27],
            ['book_id' => 29, 'author_id' => 28],
            ['book_id' => 30, 'author_id' => 29],
            ['book_id' => 31, 'author_id' => 30],
        ];
        DB::table('book_authors')->insert($bookAuthors);

        // ─── Book-Publisher relationships ───
        $bookPublishers = [
            ['book_id' => 1,  'publisher_id' => 1],
            ['book_id' => 2,  'publisher_id' => 2],
            ['book_id' => 3,  'publisher_id' => 3],
            ['book_id' => 4,  'publisher_id' => 4],
            ['book_id' => 5,  'publisher_id' => 5],
            ['book_id' => 6,  'publisher_id' => 6],
            ['book_id' => 7,  'publisher_id' => 7],
            ['book_id' => 8,  'publisher_id' => 8],
            ['book_id' => 9,  'publisher_id' => 8],
            ['book_id' => 10, 'publisher_id' => 22],
            ['book_id' => 11, 'publisher_id' => 9],
            ['book_id' => 12, 'publisher_id' => 10],
            ['book_id' => 13, 'publisher_id' => 22],
            ['book_id' => 14, 'publisher_id' => 22],
            ['book_id' => 15, 'publisher_id' => 11],
            ['book_id' => 16, 'publisher_id' => 11],
            ['book_id' => 17, 'publisher_id' => 4],
            ['book_id' => 18, 'publisher_id' => 12],
            ['book_id' => 19, 'publisher_id' => 13],
            ['book_id' => 20, 'publisher_id' => 7],
            ['book_id' => 21, 'publisher_id' => 16],
            ['book_id' => 22, 'publisher_id' => 14],
            ['book_id' => 23, 'publisher_id' => 14],
            ['book_id' => 24, 'publisher_id' => 17],
            ['book_id' => 25, 'publisher_id' => 18],
            ['book_id' => 26, 'publisher_id' => 19],
            ['book_id' => 27, 'publisher_id' => 24],
            ['book_id' => 28, 'publisher_id' => 20],
            ['book_id' => 29, 'publisher_id' => 21],
            ['book_id' => 30, 'publisher_id' => 11],
            ['book_id' => 31, 'publisher_id' => 23],
        ];
        DB::table('book_publishers')->insert($bookPublishers);

        // ─── Book copies (3 copies per book) ───
        $copyId = 1;
        $year = date('Y');
        foreach ($books as $idx => $book) {
            $bookId = $idx + 1;
            $numCopies = min($book['total_copies'], 3);
            for ($c = 1; $c <= $numCopies; $c++) {
                DB::table('book_copies')->insert([
                    'book_id'          => $bookId,
                    'accession_no'     => 'ACC' . $year . str_pad($copyId, 6, '0', STR_PAD_LEFT),
                    'barcode'          => 'BC' . str_pad($copyId, 8, '0', STR_PAD_LEFT),
                    'condition_status' => 'good',
                    'status'           => 'available',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
                $copyId++;
            }
        }

        // ─── Sample Students ───
        $students = [
            ['student_no' => '24-00001', 'library_card_no' => 'LIB202400001', 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'grade_level_id' => 10, 'section_id' => 28],
            ['student_no' => '24-00002', 'library_card_no' => 'LIB202400002', 'first_name' => 'Maria', 'last_name' => 'Santos', 'grade_level_id' => 11, 'section_id' => 31],
            ['student_no' => '24-00003', 'library_card_no' => 'LIB202400003', 'first_name' => 'Pedro', 'last_name' => 'Reyes', 'grade_level_id' => 9, 'section_id' => 25],
            ['student_no' => '24-00004', 'library_card_no' => 'LIB202400004', 'first_name' => 'Ana', 'last_name' => 'Garcia', 'grade_level_id' => 12, 'section_id' => 34],
            ['student_no' => '24-00005', 'library_card_no' => 'LIB202400005', 'first_name' => 'Jose', 'last_name' => 'Mendoza', 'grade_level_id' => 10, 'section_id' => 29],
        ];
        foreach ($students as $student) {
            DB::table('students')->insert(array_merge($student, [
                'school_year'       => '2025-2026',
                'status'            => 'active',
                'max_books_allowed' => 3,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]));
        }

        // ─── Settings ───
        $settings = [
            ['key' => 'library_name', 'value' => 'FEATI University Library', 'group' => 'general'],
            ['key' => 'school_name', 'value' => 'FEATI University', 'group' => 'general'],
            ['key' => 'academic_year', 'value' => '2025-2026', 'group' => 'general'],
            ['key' => 'borrowing_days', 'value' => '7', 'group' => 'circulation'],
            ['key' => 'max_books_student', 'value' => '3', 'group' => 'circulation'],
            ['key' => 'max_books_teacher', 'value' => '15', 'group' => 'circulation'],
            ['key' => 'max_books_employee', 'value' => '10', 'group' => 'circulation'],
            ['key' => 'teacher_loan_days', 'value' => '30', 'group' => 'circulation'],
            ['key' => 'employee_loan_days', 'value' => '21', 'group' => 'circulation'],
            ['key' => 'fine_per_day', 'value' => '5.00', 'group' => 'fines'],
            ['key' => 'max_fine', 'value' => '500.00', 'group' => 'fines'],
            ['key' => 'grace_period', 'value' => '0', 'group' => 'fines'],
            ['key' => 'max_renewals', 'value' => '2', 'group' => 'circulation'],
            ['key' => 'reservation_expiry_days', 'value' => '3', 'group' => 'circulation'],
        ];
        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
