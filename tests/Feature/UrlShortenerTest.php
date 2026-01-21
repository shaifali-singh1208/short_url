namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\ShortUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_member_cannot_create_short_urls()
    {
        $company = Company::create(['name' => 'Test Company']);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => User::ADMIN,
            'company_id' => $company->id
        ]);
        $member = User::create([
            'name' => 'Member',
            'email' => 'member@test.com',
            'password' => 'password',
            'role' => User::MEMBER,
            'company_id' => $company->id
        ]);

        $this->actingAs($admin)->post('/urls', ['long_url' => 'https://google.com'])->assertSessionHasErrors('error');
        $this->actingAs($member)->post('/urls', ['long_url' => 'https://google.com'])->assertSessionHasErrors('error');
    }

    public function test_superadmin_cannot_create_short_urls()
    {
        $superAdmin = User::create([
            'name' => 'SuperAdmin',
            'email' => 'super@test.com',
            'password' => 'password',
            'role' => User::SUPER_ADMIN
        ]);

        $this->actingAs($superAdmin)->post('/urls', ['long_url' => 'https://google.com'])->assertSessionHasErrors('error');
    }

    public function test_admin_can_only_see_urls_not_created_in_their_company()
    {
        $company1 = Company::create(['name' => 'Company 1']);
        $company2 = Company::create(['name' => 'Company 2']);

        $admin1 = User::create(['name' => 'Admin 1', 'email' => 'a1@t.com', 'password' => 'pw', 'role' => User::ADMIN, 'company_id' => $company1->id]);
        $sales2 = User::create(['name' => 'Sales 2', 'email' => 's2@t.com', 'password' => 'pw', 'role' => User::SALES, 'company_id' => $company2->id]);
        $sales1 = User::create(['name' => 'Sales 1', 'email' => 's1@t.com', 'password' => 'pw', 'role' => User::SALES, 'company_id' => $company1->id]);

        $urlInOwnCompany = ShortUrl::create(['long_url' => 'ht1', 'short_url' => 's1', 'user_id' => $sales1->id, 'company_id' => $company1->id]);
        $urlInOtherCompany = ShortUrl::create(['long_url' => 'ht2', 'short_url' => 's2', 'user_id' => $sales2->id, 'company_id' => $company2->id]);

        $response = $this->actingAs($admin1)->get('/urls');
        $response->assertStatus(200);
        $response->assertSee($urlInOtherCompany->long_url);
        $response->assertDontSee($urlInOwnCompany->long_url);
    }

    public function test_member_can_only_see_urls_not_created_by_themselves()
    {
        $company = Company::create(['name' => 'C']);
        $member = User::create(['name' => 'M', 'email' => 'm@t.com', 'password' => 'pw', 'role' => User::MEMBER, 'company_id' => $company->id]);
        $sales = User::create(['name' => 'S', 'email' => 's@t.com', 'password' => 'pw', 'role' => User::SALES, 'company_id' => $company->id]);

        $ownUrl = ShortUrl::create(['long_url' => 'ht1', 'short_url' => 's1', 'user_id' => $member->id, 'company_id' => $company->id]);
        $otherUrl = ShortUrl::create(['long_url' => 'ht2', 'short_url' => 's2', 'user_id' => $sales->id, 'company_id' => $company->id]);

        $response = $this->actingAs($member)->get('/urls');
        $response->assertSee($otherUrl->long_url);
        $response->assertDontSee($ownUrl->long_url);
    }

    public function test_short_urls_are_not_publicly_resolvable()
    {
        $company = Company::create(['name' => 'C']);
        $sales = User::create(['name' => 'S', 'email' => 's@t.com', 'password' => 'pw', 'role' => User::SALES, 'company_id' => $company->id]);
        $url = ShortUrl::create(['long_url' => 'https://google.com', 'short_url' => 'abc', 'user_id' => $sales->id, 'company_id' => $company->id]);

        // Unauthenticated
        $this->get('/u/abc')->assertRedirect('/login');

        // Authenticated
        $this->actingAs($sales)->get('/u/abc')->assertRedirect('https://google.com');
    }
}
