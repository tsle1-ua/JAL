# Architecture Overview

This project follows a layered approach based on the **service/repository** pattern. Controllers remain thin and delegate business logic to services, while services rely on repositories for all persistence operations.

## Repositories

Repositories act as the data access layer. Each domain has an interface (for example `EventRepositoryInterface`) and an implementation (such as `EloquentEventRepository`). The repository exposes simple CRUD and query methods without containing any application logic.

## Services

Services encapsulate all business logic for a domain. They receive repository interfaces through dependency injection and orchestrate any necessary operations before persisting or fetching data. A typical service looks like:

```php
class EventService
{
    public function __construct(private EventRepositoryInterface $events)
    {
        // ...
    }
}
```

Services may also interact with other Laravel facilities (validation, caching, transactions, etc.) but never directly touch HTTP requests or responses.

## Controllers and Services

Controllers inject the required service in their constructors. Every controller method calls service methods to perform actions. For instance, the `EventController` delegates to `EventService` when listing or storing events:

```php
class EventController extends Controller
{
    public function __construct(private EventService $events)
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Request $request): View
    {
        $events = $this->events->getPaginatedEvents();
        return view('events.index', compact('events'));
    }
}
```

By routing all domain logic through services, controllers stay focused on handling requests and returning responses. This separation also makes it easier to test services in isolation and swap repository implementations if needed.
