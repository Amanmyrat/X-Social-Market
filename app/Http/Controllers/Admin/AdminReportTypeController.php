<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportType\ReportTypeCreateRequest;
use App\Http\Requests\ReportType\ReportTypeDeleteRequest;
use App\Http\Requests\ReportType\ReportTypeUpdateRequest;
use App\Http\Resources\Admin\ReportType\ReportTypeResource;
use App\Http\Resources\Admin\ReportType\ReportTypeResourceCollection;
use App\Models\ReportType;
use App\Services\Admin\UniversalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReportTypeController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new ReportType());
    }

    /**
     * Report types list
     */
    public function list(Request $request): ReportTypeResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $types = $this->service->list(
            limit: $limit,
            search_query: $query,
            relationsCount: ['postReports', 'storyReports']
        );

        return new ReportTypeResourceCollection($types);
    }

    /**
     * Report type details
     */
    public function reportTypeDetails(ReportType $reportType): ReportTypeResource
    {
        return new ReportTypeResource(
            $reportType->loadCount(['postReports', 'storyReports']),
            true
        );
    }

    /**
     * Create report type
     */
    public function create(ReportTypeCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new type',
        ]);
    }

    /**
     * Update report type
     */
    public function update(ReportType $reportType, ReportTypeUpdateRequest $request): ReportTypeResource
    {
        $type = $this->service->update($reportType, $request->validated());

        return new ReportTypeResource(
            $type->loadCount(['postReports', 'storyReports']),
            true
        );
    }

    /**
     * Delete report types
     */
    public function delete(ReportTypeDeleteRequest $request): JsonResponse
    {
        $this->service->delete($request->types);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted',
        ]);
    }
}
