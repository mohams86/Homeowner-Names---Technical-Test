<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv|max:2048'
        ]);

        $fileUpload = new FileUpload();

        if($request->file()) {
            $file_name = time().'_'.$request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');

            $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
            $fileUpload->path = '/storage/' . $file_path;
            $fileUpload->save();

            $csvTempArr = [];
            $uploadedCsvPath = str_replace('\\', '/', Storage::disk('public')->path($file_path));
            $personArr = [];
            if (($open = fopen($uploadedCsvPath, "r")) !== false) {
                $lines = file($uploadedCsvPath, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
                $num_rows = count($lines);
                foreach ($lines as $line) {
                    $csv = str_getcsv($line);
                    if (count(array_filter($csv)) > 0) {
                        $csvTempArr[] = rtrim($line, ',');
                    }

                    $num_rows++;
                    unset($csvTempArr[0]);
                    $csvFinalData = array_values($csvTempArr);
                }

                foreach ($csvFinalData as $csvData) {
                    // Split the name into title and last name
                    $nameParts = explode(' ', $csvData);
                    $title = '';
                    $firstName = null;
                    $initial = null;
                    $lastName = '';

                    // Check if the name includes "and" or "&" indicating multiple individuals
                    if (in_array('and', $nameParts) || in_array('&', $nameParts)) {
                        $andIndex = array_search('and', $nameParts);
                        $ampersandIndex = array_search('&', $nameParts);

                        // Handle "Mr Tom Staff and Mr John Doe" or "Dr & Mrs Joe Bloggs" as separate individuals
                        if (($andIndex !== false && $andIndex > 0) || ($ampersandIndex !== false && $ampersandIndex > 0)) {
                            $firstPersonParts = array_slice($nameParts, 0, $andIndex !== false ? $andIndex : $ampersandIndex);
                            $secondPersonParts = array_slice($nameParts, $andIndex !== false ? $andIndex + 1 : $ampersandIndex + 1);

                            $firstPerson = $this->createPersonFromNameParts($firstPersonParts, $nameParts);
                            $secondPerson = $this->createPersonFromNameParts($secondPersonParts);

                            $personArr[] = $firstPerson;
                            $personArr[] = $secondPerson;
                        } else {
                            // Handle "Mr and Mrs Smith" as separate individuals
                            $firstPerson = $this->createPersonFromNameParts([$nameParts[0], $nameParts[2]]);
                            $secondPerson = $this->createPersonFromNameParts([$nameParts[1], $nameParts[2]]);

                            $personArr[] = $firstPerson;
                            $personArr[] = $secondPerson;
                        }
                    } else {
                        // Check if the name has a title
                        $abbreviationArr = array('Mr', 'Mrs', 'Mister', 'Dr', 'Ms', 'Prof');
                        if (in_array($nameParts[0], $abbreviationArr)) {
                            $title = $nameParts[0];
                            array_shift($nameParts);
                        }

                        // Check the first name part
                        if (count($nameParts) >= 1) {
                            $firstPart = $nameParts[0];
                            if (strlen($firstPart) === 1 && strpos($firstPart, '.') === false) {
                                $initial = $firstPart;
                            } elseif(strpos($firstPart, '.') !== false) {
                                $initial = $firstPart;
                            } else {
                                $firstName = $firstPart;
                            }
                        }

                        // Check if the name has an initial
                        if (count($nameParts) === 1) {
                            $lastPart = $nameParts[0];
                            if (strpos($lastPart, '.') !== false) {
                                $initial = $lastPart;
                            }
                        }

                        // The last part is considered as the last name
                        $lastName = end($nameParts);

                        $peopleArr = array(
                            'title' => $title,
                            'first_name' => $firstName,
                            'initial' => $initial,
                            'last_name' => $lastName
                        );

                        $personArr[] = $peopleArr;
                    }
                }
                fclose($open);
            }

            return response()->json(['success'=>'File uploaded successfully.','finalperson_arr'=>$personArr]);
        }
    }

    public function createPersonFromNameParts($nameParts, $wholeParts = null)
    {
        $title = '';
        $firstName = null;
        $initial = null;
        $lastName = '';

        // Check if the name has a title
        $abbreviationArr = array('Mr', 'Mrs', 'Mister', 'Dr', 'Ms', 'Prof');
        if (in_array($nameParts[0], $abbreviationArr)) {
            $title = $nameParts[0];
            array_shift($nameParts);
        }

        // Check if the name has an initial
        if (count($nameParts) === 1) {
            $lastPart = $nameParts[0];
            if (strlen($lastPart) === 1 && strpos($lastPart, '.') !== false) {
                $initial = $lastPart;
            } else {
                $lastName = $lastPart;
            }
        } elseif (count($nameParts) > 1) {
            $firstName = $nameParts[0];
            $lastName = $nameParts[1];
        } else {
            $andIndex = array_search('and', $wholeParts);
            $ampersandIndex = array_search('&', $wholeParts);
            if (($andIndex !== false && $andIndex > 0) || ($ampersandIndex !== false && $ampersandIndex > 0)) {
                $lastName = ($andIndex !== false) ? $wholeParts[$andIndex + 2] : (($ampersandIndex !== false) ? $wholeParts[$ampersandIndex + 3] : $lastName);
            }
        }

        $personArr = array(
            'title' => $title,
            'first_name' => $firstName,
            'initial' => $initial,
            'last_name' => $lastName
        );

        return $personArr;
    }
}
