<?php
namespace App\Utils;

class CsvReader
{
    public static function readCsv($heder_line, $filename, $delimiter = ',', $setting = [])
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        // $header = null;
        // $data = array();
        // $count = $heder_line - 1;
        // $counter = 0;
        // $skip_found = false;
        // if (($handle = fopen($filename, 'r')) !== false) {
        //     while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
        //         if (!$header) {
        //             if ($counter >= $count) {
        //                 $header = $row;

        //             }
        //             $counter++;
        //         } else {
        //             if ($setting['is_skip']) {
        //                 if ($skip_found == false) {
        //                     if (strpos(strtoupper($row[$setting['header_to_find']]), strtoupper(trim($setting['word_to_find']))) !== false) {
        //                         $skip_found = true;
        //                     }
        //                 } else {
        //                     if (strpos(strtoupper($row[$setting['header_to_find']]), strtoupper(trim($setting['word_to_find']))) !== false) {
        //                         $skip_found = true;
        //                     } else {
        //                         $data[] = array_combine($header, $row);
        //                     }
        //                 }
        //             } else {
        //                 $data[] = array_combine($header, $row);
        //             }

        //         }

        //     }
        //     fclose($handle);
        // }

        $import = new \App\Imports\CsvReader($heder_line, $setting);
        $data = \Excel::import($import, $filename);

        return $import->getData();
    }

    public static function processDataForCommission($schema, $data)
    {
        $fields_settings = json_decode($schema->schema_settings, true);

        // dd($fields_settings);
        // fields for commission_field
        //  adviser_branch_code ,adviser_name, upfront_received, upfront_paid, ongoing_paid,ongoing_received, gst, client_name, product
        // $fields_settings = [
        //     [
        //         'field' => 'adviser',
        //         'commission_field' => 'adviser_branch_code',
        //         'is_computable' => false,
        //     ],

        //     [
        //         'field' => 'ADVISER NAME',
        //         'commission_field' => 'adviser_name',
        //         'is_computable' => false,
        //     ],

        //     [
        //         'field' => 'NEW BUSINESS',
        //         'commission_field' => 'upfront_received', // can be array if on the same column other fields
        //         'is_computable' => true,
        //         'checking' => [
        //             'text_to_find' => '',
        //         ],
        //     ],

        //     [
        //         'field' => 'RENEWAL',
        //         'commission_field' => 'ongoing_paid', // can be array if on the same column other fields
        //         'is_computable' => true,
        //         'checking' => [
        //             'text_to_find' => '',
        //         ],
        //     ],

        //     [
        //         'field' => 'GST AMOUNT',
        //         'commission_field' => 'gst', // can be array if on the same column other fields
        //         'is_computable' => true,
        //         'checking' => [
        //             'text_to_find' => '',
        //         ],
        //     ],

        // ];
        // dd($data);
        $records = [];
        $adviser_name = '';
        $adviser_code = '';
        foreach ($data as $key => $row) {
            $adviser_found = true;
            $dadf = false;

            $record = [
                'adviser_branch_code' => '',
                'adviser_name' => '',
                'upfront_received' => 0,
                'upfront_paid' => 0,
                'ongoing_received' => 0,
                'ongoing_paid' => 0,
                'total' => 0,
                'gst' => 0,
                'row' => $row,
                'adviser_code' => '',
            ];
            $skiptoFind = [
                'total',
                'Account Balance Transfer',
            ];
            $fsk = true;
            foreach ($row as $k => $data) {
                $val = strtoupper(trim($data));

                foreach ($skiptoFind as $c => $k) {
                    // if (strpos(strtoupper($data), strtoupper(trim($k))) !== false) {
                    //     $fsk = false;
                    // }

                    if ($val == strtoupper(trim($k))) {
                        $fsk = false;
                    }
                }

            }

            if ($fsk) {
                foreach ($row as $k => $data) {

                    foreach ($fields_settings as $f => $field) {
                        $name = strtoupper(trim($k));
                        $val = strtoupper(trim($data));
                        $fieldArray = explode(',', $field['field']);
                        // $field_name = strtoupper(trim($field['field']));
                        $field_name = strtoupper(trim($fieldArray[0]));

                        // check the row name is equal to field name
                        if ($name == $field_name) {
                            if ($field['is_computable']) {
                                $val_convert = floatval(str_replace([',', '$'], ['', ''], $val));
                                $val_convert = self::numberFormat($val_convert);
                                foreach ($fieldArray as $fdk => $fda) {
                                    if ($name != strtoupper(trim($fda))) {
                                        if (!empty($row[trim($fda)])) {
                                            $val_convert += self::numberFormat(floatval(str_replace([',', '$'], ['', ''], $row[trim($fda)])));
                                        }

                                    }
                                }
                                $flag_text_to_find = true;
                                if (!empty($field['checking']['text_to_find'])) {
                                    if (isset($row[$field['checking']['header']]) && strtoupper($row[$field['checking']['header']]) == strtoupper(trim($field['checking']['text_to_find']))) {
                                        $flag_text_to_find = true;
                                    } else {
                                        $flag_text_to_find = false;
                                    }
                                }
                                if ($flag_text_to_find) {
                                    if (isset($field['checking']['gst_include']) && $field['checking']['gst_include'] == true) {

                                        if (isset($field['checking']['gst_include_header']) && !empty($field['checking']['gst_include_header'])) {
                                            $gst = self::numberFormat(floatval(str_replace([',', '$'], ['', ''], $row[$field['checking']['gst_include_header']])));
                                            //dd($field['checking']);
                                            if (!empty($field['checking']['text_to_find'])) {
                                                if (isset($row[$field['checking']['header']]) && strtoupper($row[$field['checking']['header']]) == strtoupper(trim($field['checking']['text_to_find']))) {

                                                    $flagGSTTextToFind = self::checkHeaderTextToFind($field, $row);

                                                    if ($flagGSTTextToFind) {
                                                        $val_convert = $val_convert - $gst;
                                                        $record['gst'] += $gst;
                                                        $record['total'] += $gst;
                                                    }

                                                }
                                            } else {

                                                $flagGSTTextToFind = self::checkHeaderTextToFind($field, $row);

                                                if ($flagGSTTextToFind) {
                                                    $val_convert = $val_convert - $gst;
                                                    $record['gst'] += $gst;
                                                    $record['total'] += $gst;
                                                }

                                            }

                                        } else {
                                            if (empty($field['checking']['gst_include_header'] && !empty($field['checking']['gst_constant_value']))) {

                                                if (!empty($field['checking']['gst_operator_type'])) {
                                                    if ($field['checking']['gst_operator_type'] == 'multiply') {
                                                        $gst = $val_convert * (floatval(str_replace([',', '$'], ['', ''], $field['checking']['gst_constant_value'])) / 100);
                                                    } else {
                                                        $gst = $val_convert / floatval(str_replace([',', '$'], ['', ''], $field['checking']['gst_constant_value']));
                                                        $gst = $val_convert - $gst;
                                                    }
                                                } else {
                                                    $gst = $val_convert * (floatval(str_replace([',', '$'], ['', ''], $field['checking']['gst_constant_value'])) / 100);
                                                }
                                                $gst = self::numberFormat($gst);
                                                if (!empty($field['checking']['text_to_find'])) {
                                                    if (isset($row[$field['checking']['header']]) && strtoupper($row[$field['checking']['header']]) == strtoupper(trim($field['checking']['text_to_find']))) {

                                                        $flagGSTTextToFind = self::checkHeaderTextToFind($field, $row);

                                                        if ($flagGSTTextToFind) {
                                                            $val_convert = $val_convert - $gst;
                                                            $record['gst'] += $gst;
                                                            $record['total'] += $gst;
                                                        }

                                                    } else {

                                                        if (empty($row[$field['checking']['header']]) && isset($field['checking']['header_empty_field'])) {

                                                            if ('upfront_paid' == $field['checking']['header_empty_field'] || 'ongoing_paid' == $field['checking']['header_empty_field']) {
                                                                $record[$field['checking']['header_empty_field']] += $val_convert;
                                                                $record['total'] -= $val_convert;
                                                            } else {
                                                                $record[$field['checking']['header_empty_field']] += $val_convert;
                                                                $record['total'] += $val_convert;
                                                            }

                                                        }
                                                    }
                                                } else {
                                                    $flagGSTTextToFind = self::checkHeaderTextToFind($field, $row);

                                                    if ($flagGSTTextToFind) {
                                                        $val_convert = $val_convert - $gst;
                                                        $record['gst'] += $gst;
                                                        $record['total'] += $gst;
                                                    }

                                                }

                                            }

                                        }
                                    } else {

                                        if (empty($field['checking']['gst_include_header'] && !empty($field['checking']['gst_constant_value']))) {
                                            if (!empty($field['checking']['gst_operator_type'])) {
                                                if ($field['checking']['gst_operator_type'] == 'multiply') {
                                                    $gst = $val_convert * (floatval(str_replace([',', '$'], ['', ''], $field['checking']['gst_constant_value'])) / 100);
                                                } else {
                                                    $gst = $val_convert / floatval(str_replace([',', '$'], ['', ''], $field['checking']['gst_constant_value']));
                                                    $gst = $val_convert - $gst;
                                                }
                                            } else {
                                                $gst = $val_convert * (floatval(str_replace([',', '$'], ['', ''], $field['checking']['gst_constant_value'])) / 100);
                                            }

                                            $gst = self::numberFormat($gst);
                                            $val_convert_exclusive_gst = $val_convert - $gst;
                                            $record['gst'] += $gst;
                                            $record['total'] += $gst;

                                        }
                                    }
                                }

                                if (!empty($field['checking']['text_to_find'])) {
                                    if (isset($row[$field['checking']['header']]) && strtoupper($row[$field['checking']['header']]) == strtoupper(trim($field['checking']['text_to_find']))) {
                                        if ('upfront_paid' == $field['commission_field'] || 'fongoing_paid' == $field['commission_field']) {
                                            $record[$field['commission_field']] += $val_convert;
                                            $record['total'] -= $val_convert;
                                        } else {
                                            $record[$field['commission_field']] += $val_convert;
                                            $record['total'] += $val_convert;
                                        }

                                    } else {

                                        if (empty($row[$field['checking']['header']]) && isset($field['checking']['header_empty_field'])) {

                                            if ('upfront_paid' == $field['checking']['header_empty_field'] || 'ongoing_paid' == $field['checking']['header_empty_field']) {
                                                $record[$field['checking']['header_empty_field']] += $val_convert;
                                                $record['total'] -= $val_convert;
                                            } else {
                                                $record[$field['checking']['header_empty_field']] += $val_convert;
                                                $record['total'] += $val_convert;
                                            }

                                        }
                                    }
                                } else {

                                    if ('upfront_paid' == $field['commission_field'] || 'ongoing_paid' == $field['commission_field']) {
                                        $record[$field['commission_field']] += $val_convert;
                                        $record['total'] -= $val_convert;
                                    } else {
                                        $record[$field['commission_field']] += $val_convert;
                                        $record['total'] += $val_convert;
                                    }
                                    // $record[$field['commission_field']] += $val_convert;
                                    // $record['total'] += $val_convert;
                                }

                            } else {

                                foreach ($fieldArray as $fdk => $fda) {

                                    if ($name != strtoupper(trim($fda))) {

                                        if (!empty($row[trim($fda)])) {

                                            $val = $val . ' ' . strtoupper(trim($row[trim($fda)]));

                                        }

                                    }
                                }

                                if ($schema->is_adviser_group && $adviser_found) {

                                    if ($field['commission_field'] == 'adviser_branch_code' || $field['commission_field'] == 'adviser_code' || $field['commission_field'] == 'adviser_name') {

                                        $separtors_array = explode(',', strtoupper($schema->adviser_group_find));

                                        $adviser_name_code = [];
                                        $found_separator = false;
                                        foreach ($separtors_array as $sp => $spv) {
                                            if ($found_separator == false) {
                                                $adviser_name_code = explode(strtoupper($spv), strtoupper(trim($val)));

                                                if (count($adviser_name_code) > 1) {
                                                    $found_separator = true;
                                                    break;
                                                }
                                            }

                                        }

                                        if (count($adviser_name_code) > 1) {
                                            $adviser_name = trim($adviser_name_code[0]);
                                            $adviser_code = trim($adviser_name_code[1]);
                                            $adviser_found = false;
                                            // dd($key, $record);
                                            continue;

                                        } else {
                                            if (!empty($field['checking']['text_to_find'])) {
                                                if (isset($row[$field['checking']['header']]) && strtoupper($row[$field['checking']['header']]) == strtoupper(trim($field['checking']['text_to_find']))) {
                                                    $record[$field['commission_field']] = trim($val);
                                                } else {

                                                    if (empty($row[$field['checking']['header']]) && isset($field['checking']['header_empty_field'])) {
                                                        $record[$field['checking']['header_empty_field']] = trim($val);
                                                    }
                                                }
                                            } else {
                                                $record[$field['commission_field']] = trim($val);
                                            }
                                        }
                                    } else {
                                        if (!empty($field['checking']['text_to_find'])) {
                                            if (isset($row[$field['checking']['header']]) && strtoupper($row[$field['checking']['header']]) == strtoupper(trim($field['checking']['text_to_find']))) {
                                                $record[$field['commission_field']] = trim($val);
                                            } else {

                                                if (empty($row[$field['checking']['header']]) && isset($field['checking']['header_empty_field'])) {
                                                    $record[$field['checking']['header_empty_field']] = trim($val);
                                                }
                                            }
                                        } else {
                                            $record[$field['commission_field']] = trim($val);
                                        }
                                    }
                                } else if ($schema->is_adviser_group && !$adviser_found) {
                                    if ($field['commission_field'] == 'adviser_branch_code' || $field['commission_field'] == 'adviser_code' || $field['commission_field'] == 'adviser_name') {
                                        $record['adviser_code'] = $adviser_code;
                                        $record['adviser_name'] = $adviser_name;
                                    } else {
                                        if (!empty($field['checking']['text_to_find'])) {
                                            if (isset($row[$field['checking']['header']]) && strtoupper($row[$field['checking']['header']]) == strtoupper(trim($field['checking']['text_to_find']))) {
                                                $record[$field['commission_field']] = trim($val);
                                            } else {

                                                if (empty($row[$field['checking']['header']]) && isset($field['checking']['header_empty_field'])) {
                                                    $record[$field['checking']['header_empty_field']] = trim($val);
                                                }
                                            }
                                        } else {

                                            $record[$field['commission_field']] = trim($val);
                                        }
                                    }
                                } else {

                                    if (!empty($field['checking']['text_to_find'])) {
                                        if (isset($row[$field['checking']['header']]) && strtoupper($row[$field['checking']['header']]) == strtoupper(trim($field['checking']['text_to_find']))) {
                                            $record[$field['commission_field']] = trim($val);
                                        } else {

                                            if (empty($row[$field['checking']['header']]) && isset($field['checking']['header_empty_field'])) {
                                                $record[$field['checking']['header_empty_field']] = trim($val);
                                            }
                                        }
                                    } else {

                                        $record[$field['commission_field']] = trim($val);

                                    }

                                }

                            }

                        }
                    }
                }

                if ($schema->is_adviser_group && ($adviser_code !== '' || $adviser_name !== '')) {
                    $record['adviser_code'] = $adviser_code;
                    $record['adviser_name'] = $adviser_name;

                } else {

                }

                if ($adviser_found && (trim(!empty($record['adviser_code'])) || !empty($record['adviser_name']))) {
                    $record['total'] = self::numberFormat($record['total']);
                    $record['upfront_received'] = self::numberFormat($record['upfront_received']);
                    $record['upfront_paid'] = self::numberFormat($record['upfront_paid']);
                    $record['ongoing_received'] = self::numberFormat($record['ongoing_received']);
                    $record['ongoing_paid'] = self::numberFormat($record['ongoing_paid']);
                    $record['gst'] = self::numberFormat($record['gst']);

                    $records[] = $record;
                } else {
                    //dd($key, $record);
                }

            } else {
                //  dd($key, $record);
            }

        }

        return $records;
    }

    public function fieldIsComputable(&$record, $row, $field, $val, $schema)
    {
        $val_convert = floatval(str_replace([',', '$'], ['', ''], $val));
        if (isset($field['checking']['gst_include']) && $field['checking']['gst_include'] == true) {

            if (isset($field['checking']['gst_include_header']) && !empty($field['checking']['gst_include_header'])) {
                $gst = floatval(str_replace([',', '$'], ['', ''], $row[$field['checking']['gst_include_header']]));
                $this->processGST($record, $field, $row, $val_convert, $gst);

            } else {
                if (empty($field['checking']['gst_include_header'] && !empty($field['checking']['gst_constant_value']))) {
                    if (!empty($field['checking']['gst_operator_type'])) {
                        if ($field['checking']['gst_operator_type'] == 'multiply') {
                            $gst = $val_convert * (floatval(str_replace([',', '$'], ['', ''], $field['checking']['gst_constant_value'])) / 100);
                        } else {
                            $gst = $val_convert / floatval(str_replace([',', '$'], ['', ''], $field['checking']['gst_constant_value']));
                            $gst = $val_convert - $gst;
                        }
                    } else {
                        $gst = $val_convert * (floatval(str_replace([',', '$'], ['', ''], $field['checking']['gst_constant_value'])) / 100);
                    }

                    if (!empty($field['checking']['text_to_find'])) {
                        if (isset($row[$field['checking']['header']]) && strtoupper($row[$field['checking']['header']]) == strtoupper(trim($field['checking']['text_to_find']))) {

                            $val_convert = $val_convert - $gst;
                            $record['gst'] += $gst;
                            $record['total'] += $gst;

                        } else {

                            if (empty($row[$field['checking']['header']]) && isset($field['checking']['header_empty_field'])) {

                                if ('upfront_paid' == $field['checking']['header_empty_field'] || 'ongoing_paid' == $field['checking']['header_empty_field']) {
                                    $record[$field['checking']['header_empty_field']] += $val_convert;
                                    $record['total'] -= $val_convert;
                                } else {
                                    $record[$field['checking']['header_empty_field']] += $val_convert;
                                    $record['total'] += $val_convert;
                                }

                            }
                        }
                    } else {

                        $val_convert = $val_convert - $gst;
                        $record['gst'] += $gst;
                        $record['total'] += $gst;

                    }

                }

            }
        }
    }

    public function processGST(&$record, $field, $row, $val_convert, $gst = 0)
    {

        if (!empty($field['checking']['text_to_find'])) {
            if (isset($row[$field['checking']['header']]) && strpos(strtoupper($row[$field['checking']['header']]), strtoupper(trim($field['checking']['text_to_find']))) !== false) {

                $val_convert = $val_convert - $gst;
                $record['gst'] += $gst;
                $record['total'] += $gst;

            }
        } else {

            $val_convert = $val_convert - $gst;
            $record['gst'] += $gst;
            $record['total'] += $gst;

        }
    }

    public static function checkHeaderTextToFind($field, $row)
    {
        if (isset($field['checking']['gst_text_to_find_header']) && !empty($field['checking']['gst_text_to_find_header']) && isset($field['checking']['gst_text_to_find_text']) && !empty($field['checking']['gst_text_to_find_text'])) {

            if (trim(strtoupper($row[$field['checking']['gst_text_to_find_header']])) == trim(strtoupper($field['checking']['gst_text_to_find_text']))) {
                return true;

            } else {
                return false;
            }

        }
        return true;
    }

    public static function numberFormat($val)
    {
        return round(number_format($val, 4, '.', ''), 2);
        //return number_format($val, 4, '.', '');
    }

    public static function readBankCsv($filename)
    {
        $import = new \App\Imports\BankCsvReader();
        $data = \Excel::import($import, $filename);

        return $import->getData();
    }
}
