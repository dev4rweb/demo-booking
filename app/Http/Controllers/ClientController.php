<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTenant;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    use ResolvesTenant;

    public function index(Request $request): Response
    {
        $tenant = $this->currentTenant($request);
        abort_unless($request->user()?->isOwnerOf($tenant), 403);

        $clients = Client::query()
            ->where('tenant_id', $tenant->id)
            ->withCount('bookings')
            ->orderBy('name')
            ->paginate(20)
            ->through(fn (Client $client): array => [
                'id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone,
                'email' => $client->email,
                'bookings_count' => $client->bookings_count,
            ]);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
        ]);
    }
}
