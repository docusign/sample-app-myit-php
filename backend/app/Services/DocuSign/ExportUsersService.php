<?php

namespace App\Services\DocuSign;

use App\Mappers\DownloadUsersMapper;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class ExportUsersService
 *
 * @package App\Services\DocuSign
 */
class ExportUsersService
{
    /**
     * File header
     */
    protected const HEADER = [
        'Name',
        'Email',
        'Permission profile',
        'Equipments',
        'Software',
    ];

    /**
     * Columns delimiter
     */
    protected const DELIMITER = ',';

    /**
     * @param DownloadUsersMapper $mapper
     */
    public function __construct(protected DownloadUsersMapper $mapper)
    {
    }

    /**
     * Create file with users
     *
     * @param Collection $employees
     * @return string
     */
    public function create(Collection $employees): string
    {
        $path = $this->createPath();
        $file = fopen($path, 'w');

        fputcsv($file, self::HEADER, self::DELIMITER);

        foreach($this->mapper->map($employees) as $employee) {
            fputcsv($file, $employee, self::DELIMITER);
        }

        fclose($file);

        return $path;
    }

    /**
     * Create path
     *
     * @return string
     */
    protected function createPath(): string
    {
        $filename = 'Users_' . uniqid() . '_' . Carbon::now()->format('Y-m-d');

        return public_path("temp/{$filename}.csv");
    }
}