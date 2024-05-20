<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class UserExportController extends Controller
{
    public function export()
    {
        $writer = SimpleExcelWriter::streamDownload(downloadName: 'users.csv', shouldAddBom: false);
        $query = User::orderBy('created_at');
        $i = 0;
        foreach ($query->lazy(1000) as $user)
        {
            $userArray = $user->toArray();
            foreach ($userArray as $key => $value) {
                $userArray[$key] = mb_convert_encoding($value, 'SJIS', 'UTF-8');
            }
            $writer->addRow($userArray);

            if ($i % 1000 === 0) {
                flush(); // Flush the buffer every 1000 rows
            }
            $i++;
        }

        $writer->toBrowser();
    }
}
