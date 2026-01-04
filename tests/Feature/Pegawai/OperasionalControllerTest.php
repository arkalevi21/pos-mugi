<?php

namespace Tests\Feature\Pegawai;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai\Pengeluaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use App\Services\Pegawai\PengeluaranService;

class OperasionalControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Login sebagai Admin
        $this->admin = User::create([
            'nama_user' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }

    public function test_index_displays_pengeluaran()
    {
        Pengeluaran::create([
            'nama_pengeluaran' => 'Beli Alat',
            'nominal' => 50000,
            'tanggal' => now(),
            'keterangan' => 'Test'
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('pengeluaran.index'));

        $response->assertStatus(200);
        $response->assertViewHas('pengeluaran');
        $response->assertViewHas('totalPengeluaran', 50000);
    }

    public function test_store_creates_pengeluaran()
    {
        $data = [
            'nama_pengeluaran' => 'Listrik',
            'nominal' => 100000,
            'tanggal' => now()->format('Y-m-d'),
            'keterangan' => 'Bulanan'
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('pengeluaran.store'), $data);

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('pengeluaran', ['nama_pengeluaran' => 'Listrik']);
    }

    public function test_update_modifies_pengeluaran()
    {
        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Lama',
            'nominal' => 10000,
            'tanggal' => now(),
            'keterangan' => '-'
        ]);

        $response = $this->actingAs($this->admin)
                         ->put(route('pengeluaran.update', $pengeluaran->id_pengeluaran), [
                             'nama_pengeluaran' => 'Baru',
                             'nominal' => 20000,
                             'tanggal' => now()->format('Y-m-d'),
                             'keterangan' => 'Update'
                         ]);

        $response->assertRedirect(route('pengeluaran.index'));
        $this->assertDatabaseHas('pengeluaran', ['nama_pengeluaran' => 'Baru']);
    }

    public function test_destroy_deletes_pengeluaran()
    {
        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Hapus',
            'nominal' => 10000,
            'tanggal' => now(),
            'keterangan' => '-'
        ]);

        $response = $this->actingAs($this->admin)
                         ->delete(route('pengeluaran.destroy', $pengeluaran->id_pengeluaran));

        $response->assertRedirect(route('pengeluaran.index'));
        $this->assertDatabaseMissing('pengeluaran', ['id_pengeluaran' => $pengeluaran->id_pengeluaran]);
    }

    // --- TEST EXCEPTION (COVER CATCH BLOCK) ---
    public function test_destroy_handles_exception()
    {
        // Kita Mock Service agar melempar Exception saat delete dipanggil
        $mockService = Mockery::mock(PengeluaranService::class);
        $mockService->shouldReceive('deletePengeluaran')
                    ->andThrow(new \Exception('Database Error'));
        
        // Inject Mock ke dalam Container Laravel
        $this->app->instance(PengeluaranService::class, $mockService);

        $response = $this->actingAs($this->admin)
                         ->delete(route('pengeluaran.destroy', 999));

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHas('error'); // Catch block triggered
    }
}