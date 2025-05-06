<?php

namespace App\Imports;

use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
// use Modules\Files\Entities\File;
// use App\Models\Product;
// use App\Models\BranchProduct;
// use Modules\Projects\Entities\Project;
use App\Models\Product;
class ProductImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // $new_file=File::create([
        //     'date'=>now(),
        //     'type'=>'projects'
        // ]);
        $createdRecords = [];
        // Iterate through the rows
        foreach ($collection as $key => $row) {
            // Log::info('Inserting response text', ['data' => $row]);
            // Skip the header row (if applicable)
            // DB::table('response_text')->insert(['text'=>json_encode($row)]);
            if ($key === 0) {
                continue;
            }
            // Save data to the database
            $new_project= Product::updateOrCreate(
                // ['id' => $row['id']], // Match by 'id' column from the file
                [
                    // 'title_ar'        => $row[0],
                    'sub_category_id'         => $row[1],
                    'name'   => $row[2],
                    'slug'   => $row[2],
                    'description'   => $row[3],
                    'price'            => $row[4],
                    'image'=>'',
                    // 'company_id'       => $row[5],
                    // 'expected_annual_return' => $row[6],
                    // 'opportunity_duration'  => $row[7],
                    // 'minimum_ammount_invest' => $row[8],
                    // 'max_ammount_invest'    => $row[9],
                    // 'lat'               => $row[10],
                    // 'lang'              => $row[11],
                    // 'image'             => $row[12], // Leave as string if it's mean
                    // 'remain_to_make'    => (int) $row[13], // Convert to int to ensure numeric values
                    // 'file_id'=>$new_file->id
                ]);
                $createdRecords[] = $new_project;
// return $new_project;
            // Create a BranchProduct entry if Product was successfully created

        }
        return $createdRecords;
    }
}
