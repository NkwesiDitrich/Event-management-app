<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Registration\RegisterForEventUseCase;
use App\Application\UseCases\Registration\UnregisterFromEventUseCase;
use App\Domain\Event\Repositories\EventRepositoryInterface;
use App\Domain\Registration\Repositories\RegistrationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    private RegisterForEventUseCase $registerUseCase;
    private UnregisterFromEventUseCase $unregisterUseCase;
    private EventRepositoryInterface $eventRepository;
    private RegistrationRepositoryInterface $registrationRepository;

    public function __construct(
        RegisterForEventUseCase $registerUseCase,
        UnregisterFromEventUseCase $unregisterUseCase,
        EventRepositoryInterface $eventRepository,
        RegistrationRepositoryInterface $registrationRepository
    ) {
        $this->registerUseCase = $registerUseCase;
        $this->unregisterUseCase = $unregisterUseCase;
        $this->eventRepository = $eventRepository;
        $this->registrationRepository = $registrationRepository;
    }

    public function register(Request $request, int $eventId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $this->registerUseCase->execute($user->getId(), $eventId);
            
            return $this->buildResponse($user->getId(), $eventId, 'Successfully registered for event!');

        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    public function unregister(Request $request, int $eventId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $this->unregisterUseCase->execute($user->getId(), $eventId);
            
            return $this->buildResponse($user->getId(), $eventId, 'Successfully unregistered from event!');

        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    private function buildResponse(int $userId, int $eventId, string $message): JsonResponse
    {
        $event = $this->eventRepository->findById($eventId);
        $isRegistered = $this->registrationRepository->existsByUserAndEvent($userId, $eventId);

        return response()->json([
            'message' => $message,
            'isRegistered' => $isRegistered,
            'currentRegistrations' => $event->getCurrentRegistrations(),
            'hasCapacityLimit' => $event->getCapacity()->isLimited(),
            'capacityLimit' => $event->getCapacity()->isLimited() ? $event->getCapacity()->getLimit() : null,
            'hasAvailableCapacity' => $event->hasAvailableCapacity()
        ]);
    }
}