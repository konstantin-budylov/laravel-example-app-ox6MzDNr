<?php

namespace App\Http\Controllers;


use App\Import\Domain\ImportedDataRepository;

class DataController extends Controller
{
    public function index(ImportedDataRepository $repository)
    {
        return response()->json($repository->getAllGroupedByDate());
    }
}
