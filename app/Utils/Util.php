<?php
namespace App\Utils;

use App\AdviserImport;
use Carbon;
use DB;
use Illuminate\Http\Request;
use Image;
use Storage;

class Util {
	public static function uploadFile(Request $request) {
		if (!Storage::disk('public')->exists('tmp/')) {
			Storage::disk('public')->makeDirectory('tmp/');
		}
		if ($request->hasFile('file')) {
			$extensions = ['csv'];
			$extensionsImage = ['jpg', 'png', 'JPG', 'PNG', 'jpeg', 'JPEG'];
			if (in_array($request->file('file')->getClientOriginalExtension(), $extensions)) {

				$orginalName = str_replace(' ', '-', $request->file('file')->getClientOriginalName());
				$exist_file = AdviserImport::where('file_name', 'like', '%' . $orginalName . '%')->where('status', 'imported')->first();
				if (!is_null($exist_file)) {
					return ['hasError' => true, 'error' => 'Similar CSV detected in the system.', 'imported' => $exist_file];
				}
				$filename = \Carbon\Carbon::now()->timestamp . str_replace(' ', '-', $request->file('file')->getClientOriginalName());
				Storage::disk('public')->put('tmp/' . $filename, \File::get($request->file('file')));
				return ['hasError' => false, 'message' => 'Successfully Upload media', 'error' => '', 'path' => storage_path('app/public') . '/tmp/' . $filename, 'url' => url('storage/tmp/' . $filename)];
			} elseif (in_array($request->file('file')->getClientOriginalExtension(), $extensionsImage)) {
				$orginalName = str_replace(' ', '-', $request->file('file')->getClientOriginalName());
				$exist_file = AdviserImport::where('file_name', 'like', '%' . $orginalName . '%')->where('status', 'imported')->first();
				if (!is_null($exist_file)) {
					return ['hasError' => true, 'error' => 'Similar File detected in the system.', 'imported' => $exist_file];
				}
				$filename = \Carbon\Carbon::now()->timestamp . str_replace(' ', '-', $request->file('file')->getClientOriginalName());
				Storage::disk('public_logos')->put('tmp/' . $filename, \File::get($request->file('file')));
				return ['hasError' => false, 'message' => 'Successfully Upload media', 'error' => '', 'path' => storage_path('app/public') . '/logos/tmp/' . $filename, 'url' => url('storage/logos/tmp/' . $filename)];
			} else {
				return ['hasError' => true, 'error' => 'Invalid File'];
			}
		} else {
			//return response()->json(['error' => 'File is empty'], 403);
			return ['hasError' => true, 'error' => 'File is empty'];
		}
	}
	public static function uploadProfilePic($user, Request $request) {
		if (!Storage::disk('public')->exists($user->hash_id . '/')) {
			Storage::disk('public')->makeDirectory($user->hash_id . '/');
		}
		if ($request->has('file')) {
			$image = Image::make($request->get('file'));
			$filename = \Carbon\Carbon::now()->timestamp . '.png';
			Storage::disk('public')->put($user->hash_id . '/' . $filename, (string) $image->encode());
			//  Storage::disk('public')->put($user->hash . '/' . $filename, \File::get($request->file('file')));
			return ['hasError' => false, 'message' => 'Successfully Upload media', 'error' => '', 'path' => storage_path('app/public') . '/' . $user->hash_id . '/' . $filename, 'url' => url('storage' . '/' . $user->hash_id . '/' . $filename), 'file' => 'storage' . '/' . $user->hash_id . '/' . $filename];
		} else {
			return ['hasError' => true, 'error' => 'File is empty'];
		}

	}
	public static function saveFile($user, $file) {
		$file_info = pathinfo($file);
		$ext = $file_info['extension'];
		$filename = uniqid() . '.' . $ext;
		Storage::disk('public')->put($user->hash_id . '/' . $filename, file_get_contents($file));

		unlink($file);
		return ['hasError' => false, 'message' => 'Successfully Upload media', 'error' => '', 'path' => storage_path('app/public') . '/' . $user->hash_id . '/' . $filename, 'url' => url('storage' . '/' . $user->hash_id . '/' . $filename), 'file' => 'storage' . '/' . $user->hash_id . '/' . $filename];
	}

	public static function segregateToAdviser($data, $company, $schema) {
		$advisers = [];

		foreach ($data as $key => $comm) {
			unset($comm['row']);
			if (empty($comm['adviser_code'])) {
				$name = self::backToNormalFormatName($comm['adviser_name']);
				$name = str_replace(['ms ', 'mr ', 'mrs '], "", trim(strtolower($name)));
				// check if the adviser is already created
				$adviser = $company->advisers()->where('name', $name)->first();

				if (!is_null($adviser)) {
					$branch = $company->adviserBranches()->where('adviser_id', $adviser->id)->where('branch_id', $schema->branch_id)->first();

					if (!is_null($branch)) {
						if (!isset($advisers[$branch->code])) {
							$comm["adviser_code"] = $branch->code;
							$advisers[$branch->code] = [
								'name' => self::backToNormalFormatName($comm['adviser_name']),
								'adviser_code' => $comm['adviser_code'],
								'comm' => [],
							];
						} else {
							$comm["adviser_code"] = $branch->code;
						}
					}
				}
			}
			if (!isset($advisers[$comm['adviser_code']])) {
				$advisers[$comm['adviser_code']] = [
					'name' => self::backToNormalFormatName($comm['adviser_name']),
					'adviser_code' => $comm['adviser_code'],
					'comm' => [],
				];
			}
			$comm['adviser_name'] = self::backToNormalFormatName($comm['adviser_name']);
			$advisers[$comm['adviser_code']]['comm'][] = $comm;
		}

		return $advisers;
	}

	public static function processRunningCommission($adviser, $running, &$summary) {

		$records = $adviser->commrecords()->select('gst',
			'upfront_received',
			'ongoing_received',
			'upfront_paid',
			'ongoing_paid',
			'total_commission',
			'client_name',
			'product',
			'product_code',
			'transaction_date',
			'id',
			'adviser_id',
			'branch_id'

		)->where(function ($query) use ($running) {

			$query->where('comm_sdate', '>=', $running->comm_sdate)->where('comm_edate', '<=', $running->comm_edate);
		})->whereNull('deleted_at')->get();

		$comm_advisers_branch = [];
		$comm_advisers_branch_total = 0;
		$recordTotal = [
			'upfront_received' => 0,
			'upfront_paid' => 0,
			'ongoing_received' => 0,
			'ongoing_paid' => 0,
			'total' => 0,
			'gst' => 0,
		];

		$total_c = 0;

		foreach ($records as $key => $record) {
			if (!isset($comm_advisers_branch[$record->branch_id])) {
				$comm_advisers_branch[$record->branch_id] = [
					'records' => [],
					'summaries' => [
						'upfront_received' => 0,
						'upfront_paid' => 0,
						'ongoing_received' => 0,
						'ongoing_paid' => 0,
						'total' => 0,
						'gst' => 0,
					],
					'branch' => $record->branch,
				];
			}

			$comm_advisers_branch[$record->branch_id]['records'][] = $record;

			$summaries = $comm_advisers_branch[$record->branch_id]['summaries'];

			$summaries['upfront_received'] += self::numberFormat($record->upfront_received);
			$summaries['upfront_paid'] += self::numberFormat($record->upfront_paid);
			$summaries['ongoing_received'] += self::numberFormat($record->ongoing_received);
			$summaries['ongoing_paid'] += self::numberFormat($record->ongoing_paid);
			$summaries['gst'] += self::numberFormat($record->gst);

			$summaries['total'] += self::numberFormat($record->upfront_received) + self::numberFormat($record->ongoing_received) + self::numberFormat($record->gst);

			$summaries['total'] -= self::numberFormat($record->ongoing_paid) + self::numberFormat($record->upfront_paid);

			$comm_advisers_branch[$record->branch_id]['summaries'] = $summaries;

			$summary['upfront_received'] += self::numberFormat($record->upfront_received);
			$summary['upfront_paid'] += self::numberFormat($record->upfront_paid);
			$summary['ongoing_received'] += self::numberFormat($record->ongoing_received);
			$summary['ongoing_paid'] += self::numberFormat($record->ongoing_paid);
			$summary['gst'] += self::numberFormat($record->gst);
			$summary['total'] += self::numberFormat($record->upfront_received) + self::numberFormat($record->ongoing_received) + self::numberFormat($record->gst);

			$summary['total'] -= self::numberFormat($record->ongoing_paid) + self::numberFormat($record->upfront_paid);

			$total_c += (self::numberFormat($record->upfront_received) + self::numberFormat($record->ongoing_received) + self::numberFormat($record->gst)) - (self::numberFormat($record->ongoing_paid) + self::numberFormat($record->upfront_paid));

			$recordTotal['upfront_received'] += self::numberFormat($record->upfront_received);
			$recordTotal['upfront_paid'] += self::numberFormat($record->upfront_paid);
			$recordTotal['ongoing_received'] += self::numberFormat($record->ongoing_received);
			$recordTotal['ongoing_paid'] += self::numberFormat($record->ongoing_paid);
			$recordTotal['gst'] += self::numberFormat($record->gst);
			$recordTotal['total'] += self::numberFormat($record->upfront_received) + self::numberFormat($record->ongoing_received) + self::numberFormat($record->gst);

			$recordTotal['total'] -= self::numberFormat($record->ongoing_paid) + self::numberFormat($record->upfront_paid);

			$comm_advisers_branch_total += self::numberFormat($record->upfront_received) + self::numberFormat($record->ongoing_received) + self::numberFormat($record->gst);
			$comm_advisers_branch_total -= self::numberFormat($record->ongoing_paid) + self::numberFormat($record->upfront_paid);
		}

		if ($total_c < 0) {
			$summary['total_negative'] += self::numberFormat($total_c);
		} else {
			$summary['total_w_o_fees_e_neg'] += self::numberFormat($total_c);
			$summary['total_e_neg'] += self::numberFormat($total_c);
		}
		$summary['total_w_o_fees'] += self::numberFormat($total_c);

		return ['branches' => $comm_advisers_branch, 'total' => $comm_advisers_branch_total, 'recordTotal' => $recordTotal];
	}

	public static function processFeesAdviser($adviser, &$summary, $runnings) {
		$records = [
			'fees' => [],
			'summaries' => [
				'total' => 0,
				'take' => 0,
				'give' => 0,
			],
		];

		$adviser_summary = $runnings->adviserSummaries()->where('adviser_id', $adviser->id)->first();
		// dd([$runnings, $adviser_summary->adviserFees]);

		if (is_null($adviser_summary)) {
			$fees = $adviser->adviserFees()->where(function ($query) {
				$query->where(function ($query) {
					$query->where('re_occurrence', 'Forever')->whereNull('deleted_at');
				})->orWhere(function ($query) {
					$query->where('re_occurrence', 'Limited')->where('re_occurrence_running_times', '>', 0)->whereNull('deleted_at');
				})
					->orWhere(function ($query) {
						$query->where('re_occurrence', 'One-off')->where('re_occurrence_running_times', '>', 0)->whereNull('deleted_at');
					})->orWhere(function ($query) {
					$query->where('re_occurrence', 'Specific Date')->where('re_occurrence_date', 'like', '%' . date('Y-m-d') . '%')->whereNull('deleted_at');
				});
			})->get();

		} else {
			$fees = $adviser_summary->adviserFees;
		}

		$total = 0;
		$take = 0;
		$give = 0;
		$totalCarried = 0;
		foreach ($fees as $key => $fee) {

			if ($fee->action_type == 'Give') {
				$total += $fee->fee_amount;
				$give += $fee->fee_amount;
				$summary['fees']['total'] += $fee->fee_amount;
				$summary['fees']['give'] += $fee->fee_amount;
			}

			if ($fee->action_type == 'Take') {
				$total -= $fee->fee_amount;
				$take += $fee->fee_amount;

				$summary['fees']['total'] -= $fee->fee_amount;
				$summary['fees']['take'] += $fee->fee_amount;
				//$fee->fee_amount *= -1;
			}
			$records['fees'][] = $fee;
		}

		if (is_null($adviser_summary)) {

			$fees = $adviser->company->fees()->where(function ($query) {
				$query->where(function ($query) {
					$query->where('re_occurrence', 'Forever')->whereNull('deleted_at');
				})->orWhere(function ($query) {
					$query->where('re_occurrence', 'Limited')->where('re_occurrence_running_times', '>', 0);
				})
					->orWhere(function ($query) {
						$query->where('re_occurrence', 'One-off')->where('re_occurrence_running_times', '>', 0);
					})->orWhere(function ($query) {
					$query->where('re_occurrence', 'Specific Date')->where('re_occurrence_date', 'like', '%' . date('Y-m-d') . '%');
				});
			})->get();
		} else {
			$fees = $adviser_summary->fees;
		}
		foreach ($fees as $key => $fee) {

			if ($fee->action_type == 'Give') {
				$total += $fee->fee_amount;
				$give += $fee->fee_amount;
				$summary['fees']['total'] += $fee->fee_amount;
				$summary['fees']['give'] += $fee->fee_amount;
			}

			if ($fee->action_type == 'Take') {
				$total -= $fee->fee_amount;
				$take += $fee->fee_amount;

				$summary['fees']['total'] -= $fee->fee_amount;
				$summary['fees']['take'] += $fee->fee_amount;
				// $fee->fee_amount *= -1;
			}

			$records['fees'][] = $fee;

		}

		if ($runnings->prev_running_id > 0 && is_null($adviser_summary)) {
			$summaryFee = \App\RunningAdviserSummary::where('running_commission_id', $runnings->prev_running_id)->where('adviser_id', $adviser->id)->first();

			if (!is_null($summaryFee)) {
				$total_with_dist = self::numberFormat($summaryFee->total_adviser_dist, 2, '.', '') + self::numberFormat($summaryFee->total_fee, 2, '.', '');

				if ($total_with_dist < 0) {
					$nameCommission = date('d-F Y', strtotime($summaryFee->running->comm_edate));
					$adviserFee = new \App\AdviserFee;
					$adviserFee->fee_name = 'Negative Carried Forward - ' . $nameCommission;
					$adviserFee->adviser_id = $adviser->id;
					$adviserFee->fee_amount = $total_with_dist * -1;
					$adviserFee->action_type = 'Take';
					$adviserFee->re_occurrence = 'One-off';
					$adviserFee->re_occurrence_date = null;
					$adviserFee->re_occurrence_times = 1;
					$adviserFee->re_occurrence_running_times = 1;
					$adviserFee->user_id = $runnings->user_id;
					$adviserFee->company_id = $runnings->company_id;
					$adviserFee->hash_id = hash('sha256', uniqid() . time());
					$total -= $adviserFee->fee_amount;
					$take += $adviserFee->fee_amount;

					$summary['fees']['total'] -= $adviserFee->fee_amount;
					$summary['fees']['take'] += $adviserFee->fee_amount;
					$summary['fees']['totalCarried'] += $adviserFee->fee_amount;

					$records['fees'][] = $adviserFee;
					$totalCarried += $adviserFee->fee_amount;
				}
			}

		}
		$records['summaries'] = [
			'total' => $total,
			'take' => $take,
			'give' => $give,
			'totalCarried' => $totalCarried,
		];

		$summary['total_e_neg'] += self::numberFormat($total);

		return $records;
	}

	public static function processFeesTeam($team, &$summary, $runnings) {
		$records = [
			'fees' => [],
			'summaries' => [
				'total' => 0,
				'take' => 0,
				'give' => 0,
			],
		];

		$team_summary = $runnings->teamSummaries()->where('team_id', $team->id)->first();

		if (is_null($team_summary)) {
			$fees = $team->fees()->where(function ($query) {
				$query->where(function ($query) {
					$query->where('re_occurrence', 'Forever')->whereNull('deleted_at');
				})->orWhere(function ($query) {
					$query->where('re_occurrence', 'Limited')->where('re_occurrence_running_times', '>', 0)->whereNull('deleted_at');
				})
					->orWhere(function ($query) {
						$query->where('re_occurrence', 'One-off')->where('re_occurrence_running_times', '>', 0)->whereNull('deleted_at');
					})->orWhere(function ($query) {
					$query->where('re_occurrence', 'Specific Date')->where('re_occurrence_date', 'like', '%' . date('Y-m-d') . '%')->whereNull('deleted_at');
				});
			})->get();
		} else {
			$fees = $team_summary->adviserFees;
		}

		$total = 0;
		$take = 0;
		$give = 0;
		$totalCarried = 0;
		foreach ($fees as $key => $fee) {

			if ($fee->action_type == 'Give') {
				$total += $fee->fee_amount;
				$give += $fee->fee_amount;
				$summary['fees']['total'] += $fee->fee_amount;
				$summary['fees']['give'] += $fee->fee_amount;
			}

			if ($fee->action_type == 'Take') {
				$total -= $fee->fee_amount;
				$take += $fee->fee_amount;

				$summary['fees']['total'] -= $fee->fee_amount;
				$summary['fees']['take'] += $fee->fee_amount;
				//$fee->fee_amount *= -1;
			}
			$records['fees'][] = $fee;
		}

		if (is_null($team_summary)) {
			$fees = $team->company->fees()->where(function ($query) {
				$query->where(function ($query) {
					$query->where('re_occurrence', 'Forever')->whereNull('deleted_at');
				})->orWhere(function ($query) {
					$query->where('re_occurrence', 'Limited')->where('re_occurrence_running_times', '>', 0);
				})
					->orWhere(function ($query) {
						$query->where('re_occurrence', 'One-off')->where('re_occurrence_running_times', '>', 0);
					})->orWhere(function ($query) {
					$query->where('re_occurrence', 'Specific Date')->where('re_occurrence_date', 'like', '%' . date('Y-m-d') . '%');
				});
			})->get();
		} else {
			$fees = $team_summary->fees;
		}
		foreach ($fees as $key => $fee) {

			if ($fee->action_type == 'Give') {
				$total += $fee->fee_amount;
				$give += $fee->fee_amount;
				$summary['fees']['total'] += $fee->fee_amount;
				$summary['fees']['give'] += $fee->fee_amount;
			}

			if ($fee->action_type == 'Take') {
				$total -= $fee->fee_amount;
				$take += $fee->fee_amount;

				$summary['fees']['total'] -= $fee->fee_amount;
				$summary['fees']['take'] += $fee->fee_amount;
				//$fee->fee_amount *= -1;
			}

			$records['fees'][] = $fee;

		}

		if ($runnings->prev_running_id > 0 && is_null($team_summary)) {
			$summaryFee = \App\RunningTeamSummary::where('running_commission_id', $runnings->prev_running_id)->where('team_id', $team->id)->first();

			if (!is_null($summaryFee)) {
				$total_with_dist = self::numberFormat($summaryFee->total_adviser_dist, 2, '.', '') + self::numberFormat($summaryFee->total_fee, 2, '.', '');

				if ($total_with_dist < 0) {
					$nameCommission = date('d-F Y', strtotime($summaryFee->running->comm_edate));
					$adviserFee = new \App\TeamFee;
					$adviserFee->fee_name = 'Negative Carried Forward - ' . $nameCommission;
					$adviserFee->team_id = $team->id;
					$adviserFee->fee_amount = $total_with_dist * -1;
					$adviserFee->action_type = 'Take';
					$adviserFee->re_occurrence = 'One-off';
					$adviserFee->re_occurrence_date = null;
					$adviserFee->re_occurrence_times = 1;
					$adviserFee->re_occurrence_running_times = 1;
					$adviserFee->user_id = $runnings->user_id;
					$adviserFee->company_id = $runnings->company_id;
					$adviserFee->hash_id = hash('sha256', uniqid() . time());
					$total -= $adviserFee->fee_amount;
					$take += $adviserFee->fee_amount;

					$summary['fees']['total'] -= $adviserFee->fee_amount;
					$summary['fees']['take'] += $adviserFee->fee_amount;
					$summary['fees']['totalCarried'] += $adviserFee->fee_amount;
					$records['fees'][] = $adviserFee;
					$totalCarried += $adviserFee->fee_amount;
				}
			}

		}
		$records['summaries'] = [
			'total' => $total,
			'take' => $take,
			'give' => $give,
			'totalCarried' => $totalCarried,
		];

		$summary['total_e_neg'] += self::numberFormat($total);

		return $records;
	}

	public static function processPercentageDistrubution($adviser, &$summary, $adviserTotal, $feesTotal) {
		if ($adviser->override_company_percent) {
			$percentage_company = $adviser->company_comm_percent;
			$percentage_adviser = $adviser->adviser_comm_percent;
		} else {
			$percentage_company = $adviser->company->company_comm_percent;
			$percentage_adviser = $adviser->company->adviser_comm_percent;
		}

		$computed_adviser_total = $adviserTotal - ($adviserTotal * ($percentage_company / 100));

		$company_distribution_total = $adviserTotal - $computed_adviser_total;

		$dist_total = $adviserTotal + $feesTotal - $company_distribution_total;
		$summary['company_distribution_total'] += $company_distribution_total;
		if ($dist_total > 0) {

			$summary['distribution_total'] += $dist_total;
		}

		if ($company_distribution_total > 0) {
			//$summary['company_distribution_total'] += $company_distribution_total;
		}
		return ['company_dist_value' => $adviserTotal - $computed_adviser_total, 'computed_adviser_total' => $computed_adviser_total, 'label' => 'Company Percentage Distribution ' . $percentage_company . '%'];
	}

	public static function processPercentageDistrubutionTeam($team, &$summary, $adviserTotal, $feesTotal) {
		if ($team->override_company_percent) {
			$percentage_company = $team->company_comm_percent;
			$percentage_adviser = $team->team_comm_percent;
		} else {
			$percentage_company = $team->company->company_comm_percent;
			$percentage_adviser = $team->company->adviser_comm_percent;
		}

		$computed_adviser_total = $adviserTotal - ($adviserTotal * ($percentage_company / 100));

		$company_distribution_total = $adviserTotal - $computed_adviser_total;

		$dist_total = $adviserTotal + $feesTotal - $company_distribution_total;
		$summary['company_distribution_total'] += $company_distribution_total;
		if ($dist_total > 0) {

			$summary['distribution_total'] += $dist_total;
		}

		if ($company_distribution_total > 0) {
			//$summary['company_distribution_total'] += $company_distribution_total;
		}
		return ['company_dist_value' => $adviserTotal - $computed_adviser_total, 'computed_adviser_total' => $computed_adviser_total, 'label' => 'Company Percentage Distribution ' . $percentage_company . '%'];
	}
	public static function savePdfFile($running, $adviser, $summaryAdviser, $summaryBranches, $filename, $view, $ytd) {
		// if (!Storage::disk('public')->exists($adviser->hash_id . '/')) {
		//     Storage::disk('public')->makeDirectory($adviser->hash_id . '/');

		//     if (!Storage::disk('public')->exists($adviser->hash_id . '/' . $summaryAdviser->hash_id)) {
		//         Storage::disk('public')->makeDirectory($adviser->hash_id . '/' . $summaryAdviser->hash_id);
		//     }
		// } else {
		//     if (!Storage::disk('public')->exists($adviser->hash_id . '/' . $summaryAdviser->hash_id)) {
		//         Storage::disk('public')->makeDirectory($adviser->hash_id . '/' . $summaryAdviser->hash_id);
		//     }
		// }

		// $path = storage_path('app/public') . '/' . $adviser->hash_id . '/' . $summaryAdviser->hash_id;

		$records = [];

		$summaryRecords = $summaryAdviser->records()->where('adviser_id', $summaryAdviser->adviser_id)->join('branches', 'branches.id', '=', 'adviser_commission_records.branch_id')->orderBy('branches.name', 'ASC')->orderBy('client_name', 'ASC')->get();
		foreach ($summaryRecords as $c => $rc) {
			if (!isset($records[$rc->branch_id])) {
				$records[$rc->branch_id] = [
					'branch' => $rc->branch,
					'records' => [],
				];

			}
			$records[$rc->branch_id]['records'][] = $rc;
		}
		$pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView($view, ['running' => $running, 'adviser' => $adviser, 'summaryAdviser' => $summaryAdviser, 'summaryBranches' => $summaryBranches, 'records' => $records, 'ytd' => $ytd]);
		$rawPdf = $pdf;
		$data = $pdf->setPaper('a4', 'landscape')->output();

		return ['filename' => $filename, 'pdf' => $data, 'rawPdf' => $rawPdf];
	}

	public static function savePdfFileInvoice($invoice, $view, $filename) {
		$pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView($view, ['invoice' => $invoice]);
		$rawPdf = $pdf;
		$data = $pdf->setPaper('a4')->output();

		return ['filename' => $filename, 'pdf' => $data, 'rawPdf' => $rawPdf];
	}

	public static function downloadPdfFileInvoice($invoice, $view, $filename) {
		$pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView($view, ['invoice' => $invoice]);

		$data = $pdf->setPaper('a4');

		return $data;
	}
	public static function downloadPdfFile($running, $adviser, $summaryAdviser, $summaryBranches, $filename, $view, $ytd) {
		// if (!Storage::disk('public')->exists($adviser->hash_id . '/')) {
		//     Storage::disk('public')->makeDirectory($adviser->hash_id . '/');

		//     if (!Storage::disk('public')->exists($adviser->hash_id . '/' . $summaryAdviser->hash_id)) {
		//         Storage::disk('public')->makeDirectory($adviser->hash_id . '/' . $summaryAdviser->hash_id);
		//     }
		// } else {
		//     if (!Storage::disk('public')->exists($adviser->hash_id . '/' . $summaryAdviser->hash_id)) {
		//         Storage::disk('public')->makeDirectory($adviser->hash_id . '/' . $summaryAdviser->hash_id);
		//     }
		// }

		// $path = storage_path('app/public') . '/' . $adviser->hash_id . '/' . $summaryAdviser->hash_id;

		$records = [];

		$summaryRecords = $summaryAdviser->records()->join('branches', 'branches.id', '=', 'adviser_commission_records.branch_id')->orderBy('branches.name', 'ASC')->orderBy('client_name', 'ASC')->get();
		foreach ($summaryRecords as $c => $rc) {
			if (!isset($records[$rc->branch_id])) {
				$records[$rc->branch_id] = [
					'branch' => $rc->branch,
					'records' => [],
				];

			}
			$records[$rc->branch_id]['records'][] = $rc;
		}

		$pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView($view, ['running' => $running, 'adviser' => $adviser, 'summaryAdviser' => $summaryAdviser, 'summaryBranches' => $summaryBranches, 'records' => $records, 'ytd' => $ytd]);
		$pdf->setPaper('a4', 'landscape');

		return $pdf;
	}

	public static function downloadPdfFileTeam($running, $team, $summaryTeam, $adviserSummeries, $filename, $view, $ytd) {

		$pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView($view, ['running' => $running, 'team' => $team, 'teamSummary' => $summaryTeam, 'summaryAdvisers' => $adviserSummeries, 'ytd' => $ytd]);
		$pdf->setPaper('a4', 'landscape');

		return $pdf;
	}
	public static function savePdfFileTeam($running, $team, $summaryTeam, $adviserSummeries, $filename, $view, $ytd) {

		$pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView($view, ['running' => $running, 'team' => $team, 'teamSummary' => $summaryTeam, 'summaryAdvisers' => $adviserSummeries, 'ytd' => $ytd]);

		$rawPdf = $pdf;
		$data = $pdf->setPaper('a4', 'landscape')->output();

		return ['filename' => $filename, 'pdf' => $data, 'rawPdf' => $rawPdf];
	}

	public static function backToNormalFormatName($name) {
		$array = explode(',', $name);
		$result = '';
		for ($i = count($array) - 1; $i >= 0; $i--) {

			if ($i == count($array) - 1) {
				$result .= $array[$i];
			} else {
				$result .= ' ' . $array[$i];
			}
		}
		return $result;
	}

	public static function roundNumber($value) {
		return round($value, 2);
	}
	public static function numberFormat($value, $d = 4, $s = '.', $f = '') {
		if ($value < 0) {
			$value = $value * -1;

			$value = round(number_format($value, 4, $s, $f), 2);

			if ($value == 0) {
				return 0;
			} else {
				return $value * -1;
			}

		}

		return round(number_format($value, 4, $s, $f), 2);
	}

	public static function processYTD($company, $adviser, $comm = null) {

		$total_gst = 0;
		$total_upfront_received = 0;
		$total_ongoing_received = 0;
		$total_upfront_paid = 0;
		$total_ongoing_paid = 0;
		$total_commission = 0;
		$total_fee = 0;
		$total_take = 0;
		$total_give = 0;
		$total_dist = 0;
		$total_ng_forward = 0;
		$total_with_dist = 0;
		$date = self::getDatesYTD();
		// ->where(function ($query) use ($date) {
		//     $query->where('comm_sdate', '>=', $date[0])->where('comm_edate', '<=', $date[1]);
		// })
		if (is_null($comm)) {
			$runnings = $company->commissions()->where('status', '!=', 0)->whereHas('adviserSummaries', function ($query) use ($adviser) {
				$query->where('adviser_id', $adviser->id);
			})->get(['id'])->toArray();
		} else {
			$runnings = $company->commissions()->where('status', '!=', 0)->whereHas('adviserSummaries', function ($query) use ($adviser) {
				$query->where('adviser_id', $adviser->id);
			})->where('comm_edate', '<=', $comm->comm_edate)->get(['id'])->toArray();
		}

		$summaries = \App\RunningAdviserSummary::whereIn('running_commission_id', $runnings)->where('adviser_id', $adviser->id)->get();
		$branches = [];
		$branchesTotal = [
			'total_gst' => 0,
			'total_upfront_received' => 0,
			'total_ongoing_received' => 0,
			'total_upfront_paid' => 0,
			'total_ongoing_paid' => 0,
			'total_commission' => 0,
		];
		foreach ($summaries as $key => $summary) {

			$total_gst += self::numberFormat($summary->total_gst, 2, '.', '');
			$total_upfront_received += self::numberFormat($summary->total_upfront_received, 2, '.', '');
			$total_ongoing_received += self::numberFormat($summary->total_ongoing_received, 2, '.', '');
			$total_upfront_paid += self::numberFormat($summary->total_upfront_paid, 2, '.', '');
			$total_ongoing_paid += self::numberFormat($summary->total_ongoing_paid, 2, '.', '');
			$total_commission += self::numberFormat($summary->total_commission, 2, '.', '');
			// $total_fee += self::numberFormat($summary->total_fee, 2, '.', '');
			// $total_take += self::numberFormat($summary->total_take, 2, '.', '');
			// $total_give += self::numberFormat($summary->total_give, 2, '.', '');
			$total_with_dist += self::numberFormat($summary->total_adviser_dist);
			foreach ($summary->fees as $f => $fee) {
				if (strpos($fee->fee_name, 'Negative Carried') !== false || strpos($fee->fee_name, 'Carried') !== false) {
					//$total_with_dist -= self::numberFormat($fee->fee_amount, 2, '.', '');
					// $total_ng_forward += self::numberFormat($fee->fee_amount, 2, '.', '');
				} else {
					if ($fee->action_type == 'Take') {
						// tanya said only take the forever recuring
						// if ($fee->re_occurrence == 'Forever') {

						// }
						$total_fee += self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_take += self::numberFormat($fee->fee_amount, 2, '.', '');
					} else {
						// $total_fee -= self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_give += self::numberFormat($fee->fee_amount, 2, '.', '');
					}
				}

			}

			foreach ($summary->adviserFees as $f => $fee) {
				if (strpos($fee->fee_name, 'Negative Carried') !== false || strpos($fee->fee_name, 'Carried') !== false) {
					//$total_with_dist -= self::numberFormat($fee->fee_amount, 2, '.', '');
					// $total_ng_forward += self::numberFormat($fee->fee_amount, 2, '.', '');
				} else {
					if ($fee->action_type == 'Take') {
						// tanya said only take the forever recuring
						// if ($fee->re_occurrence == 'Forever') {

						// }
						$total_fee += self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_take += self::numberFormat($fee->fee_amount, 2, '.', '');
					} else {
						// $total_fee -= self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_give += self::numberFormat($fee->fee_amount, 2, '.', '');
					}
				}

			}

			$total_dist += self::numberFormat($summary->total_company_dist, 2, '.', '');
			// $total_with_dist += self::numberFormat($summary->total_adviser_dist, 2, '.', '') + self::numberFormat($total_fee, 2, '.', '');

			foreach ($summary->branchSummaries as $br => $branchSummary) {
				if (!isset($branches[$branchSummary->branch_id])) {
					$branches[$branchSummary->branch_id] = [
						'branch' => $branchSummary->branch,
						'total_gst' => 0,
						'total_upfront_received' => 0,
						'total_ongoing_received' => 0,
						'total_upfront_paid' => 0,
						'total_ongoing_paid' => 0,
						'total_commission' => 0,

					];
				}

				$branches[$branchSummary->branch_id]['total_gst'] += self::numberFormat($branchSummary->total_gst, 2, '.', '');
				$branches[$branchSummary->branch_id]['total_upfront_received'] += self::numberFormat($branchSummary->total_upfront_received, 2, '.', '');
				$branches[$branchSummary->branch_id]['total_ongoing_received'] += self::numberFormat($branchSummary->total_ongoing_received, 2, '.', '');
				$branches[$branchSummary->branch_id]['total_upfront_paid'] += self::numberFormat($branchSummary->total_upfront_paid, 2, '.', '');
				$branches[$branchSummary->branch_id]['total_ongoing_paid'] += self::numberFormat($branchSummary->total_ongoing_paid, 2, '.', '');
				$branches[$branchSummary->branch_id]['total_commission'] += self::numberFormat($branchSummary->total_commission, 2, '.', '');

				$branchesTotal['total_gst'] += self::numberFormat($branchSummary->total_gst, 2, '.', '');
				$branchesTotal['total_upfront_received'] += self::numberFormat($branchSummary->total_upfront_received, 2, '.', '');
				$branchesTotal['total_ongoing_received'] += self::numberFormat($branchSummary->total_ongoing_received, 2, '.', '');
				$branchesTotal['total_upfront_paid'] += self::numberFormat($branchSummary->total_upfront_paid, 2, '.', '');
				$branchesTotal['total_ongoing_paid'] += self::numberFormat($branchSummary->total_ongoing_paid, 2, '.', '');
				$branchesTotal['total_commission'] += self::numberFormat($branchSummary->total_commission, 2, '.', '');

			}
		}
		$total_with_dist = $total_with_dist - $total_fee;
		return [

			'adviser' => $adviser,
			'branches' => $branches,
			'branchesTotal' => $branchesTotal,
			'total_gst' => $total_gst,
			'total_upfront_received' => $total_upfront_received,
			'total_ongoing_received' => $total_ongoing_received,
			'total_upfront_paid' => $total_upfront_paid,
			'total_ongoing_paid' => $total_ongoing_paid,
			'total_commission' => $total_commission,
			'total_fee' => $total_fee,
			'total_take' => $total_take,
			'total_give' => $total_give,
			'total_dist' => $total_dist,
			'total_with_dist' => $total_with_dist,
		];

	}

	public static function processYTDTeam($company, $team, $comm = null) {

		$total_gst = 0;
		$total_upfront_received = 0;
		$total_ongoing_received = 0;
		$total_upfront_paid = 0;
		$total_ongoing_paid = 0;
		$total_commission = 0;
		$total_fee = 0;
		$total_take = 0;
		$total_give = 0;
		$total_dist = 0;
		$total_with_dist = 0;
		$total_ng_forward = 0;

		$date = self::getDatesYTD();
		// ->where(function ($query) use ($date) {
		//     $query->where('comm_sdate', '>=', $date[0])->where('comm_edate', '<=', $date[1]);
		// })
		if (is_null($comm)) {
			$runnings = $company->commissions()->where('status', '!=', 0)->whereHas('teamSummaries', function ($query) use ($team) {
				$query->where('team_id', $team->id);
			})->get(['id'])->toArray();
		} else {
			$runnings = $company->commissions()->where('status', '!=', 0)->whereHas('teamSummaries', function ($query) use ($team) {
				$query->where('team_id', $team->id);
			})->where('comm_edate', '<=', $comm->comm_edate)->get(['id'])->toArray();
		}

		$summaries = \App\RunningTeamSummary::whereIn('running_commission_id', $runnings)->where('team_id', $team->id)->get();

		$branches = [];
		$branchesTotal = [
			'total_gst' => 0,
			'total_upfront_received' => 0,
			'total_ongoing_received' => 0,
			'total_upfront_paid' => 0,
			'total_ongoing_paid' => 0,
			'total_commission' => 0,
		];

		$advisers = [];
		$advisersTotal = [
			'total_gst' => 0,
			'total_upfront_received' => 0,
			'total_ongoing_received' => 0,
			'total_upfront_paid' => 0,
			'total_ongoing_paid' => 0,
			'total_commission' => 0,
		];
		$feesDD = [
			'fees' => [],
			'adviserFees' => [],
			'give' => [],
			'total' => [],
			'totalF' => 0,
		];
		foreach ($summaries as $key => $summary) {

			$total_gst += self::numberFormat($summary->total_gst, 2, '.', '');
			$total_upfront_received += self::numberFormat($summary->total_upfront_received, 2, '.', '');
			$total_ongoing_received += self::numberFormat($summary->total_ongoing_received, 2, '.', '');
			$total_upfront_paid += self::numberFormat($summary->total_upfront_paid, 2, '.', '');
			$total_ongoing_paid += self::numberFormat($summary->total_ongoing_paid, 2, '.', '');
			$total_commission += self::numberFormat($summary->total_commission, 2, '.', '');
			// $total_fee += self::numberFormat($summary->total_fee, 2, '.', '');
			// $total_take += self::numberFormat($summary->total_take, 2, '.', '');
			// $total_give += self::numberFormat($summary->total_give, 2, '.', '');
			$total_with_dist += self::numberFormat($summary->total_adviser_dist);
			$total_summary_fee_take = 0;
			$fdf = [];
			foreach ($summary->fees as $f => $fee) {
				if (strpos($fee->fee_name, 'Negative Carried') !== false || strpos($fee->fee_name, 'Carried') !== false) {
					// $total_with_dist -= self::numberFormat($fee->fee_amount, 2, '.', '');
					$total_ng_forward += self::numberFormat($fee->fee_amount, 2, '.', '');
				} else {
					if ($fee->action_type == 'Take') {
						// tanya said only take the forever recuring
						// if ($fee->re_occurrence == 'Forever') {

						// }
						$total_fee += self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_take += self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_summary_fee_take += self::numberFormat($fee->fee_amount, 2, '.', '');
						$fdf[] = $fee->fee_name . '- ' . $fee->fee_amount;
					} else {
						// $total_fee -= self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_give += self::numberFormat($fee->fee_amount, 2, '.', '');
						$feesDD['give'][] = $fee;
					}
				}

				$feesDD['fees'][] = $fee;

			}

			foreach ($summary->adviserFees as $f => $fee) {
				if (strpos($fee->fee_name, 'Negative Carried') !== false || strpos($fee->fee_name, 'Carried') !== false) {
					// $total_with_dist -= self::numberFormat($fee->fee_amount, 2, '.', '');
					$total_ng_forward += self::numberFormat($fee->fee_amount, 2, '.', '');
				} else {
					if ($fee->action_type == 'Take') {
						// tanya said only take the forever recuring
						// if ($fee->re_occurrence == 'Forever') {

						// }
						$total_fee += self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_take += self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_summary_fee_take += self::numberFormat($fee->fee_amount, 2, '.', '');
						$fdf[] = $fee->fee_name . '- ' . $fee->fee_amount;
					} else {
						// $total_fee -= self::numberFormat($fee->fee_amount, 2, '.', '');
						$total_give += self::numberFormat($fee->fee_amount, 2, '.', '');
						$feesDD['give'][] = $fee;
					}
				}
				$feesDD['adviserFees'][] = $fee;
			}
			$feesDD['total'][] = [$total_summary_fee_take, $summary, $fdf, $summary->running->name];

			$total_dist += self::numberFormat($summary->total_company_dist, 2, '.', '');
			//$total_with_dist -= self::numberFormat($total_fee, 2, '.', '');

			foreach ($summary->adviserSummaries as $c => $sum) {

				if (!isset($advisers[$sum->adviser_id])) {
					$advisers[$sum->adviser_id] = [
						'adviser' => $sum->adviser,
						'total_gst' => 0,
						'total_upfront_received' => 0,
						'total_ongoing_received' => 0,
						'total_upfront_paid' => 0,
						'total_ongoing_paid' => 0,
						'total_commission' => 0,

					];
				}
				$advisersTotal['total_gst'] += self::numberFormat($sum->total_gst, 2, '.', '');
				$advisersTotal['total_upfront_received'] += self::numberFormat($sum->total_upfront_received, 2, '.', '');
				$advisersTotal['total_ongoing_received'] += self::numberFormat($sum->total_ongoing_received, 2, '.', '');
				$advisersTotal['total_upfront_paid'] += self::numberFormat($sum->total_upfront_paid, 2, '.', '');
				$advisersTotal['total_ongoing_paid'] += self::numberFormat($sum->total_ongoing_paid, 2, '.', '');
				$advisersTotal['total_commission'] += self::numberFormat($sum->total_commission, 2, '.', '');

				$advisers[$sum->adviser_id]['total_gst'] += self::numberFormat($sum->total_gst, 2, '.', '');
				$advisers[$sum->adviser_id]['total_upfront_received'] += self::numberFormat($sum->total_upfront_received, 2, '.', '');
				$advisers[$sum->adviser_id]['total_ongoing_received'] += self::numberFormat($sum->total_ongoing_received, 2, '.', '');
				$advisers[$sum->adviser_id]['total_upfront_paid'] += self::numberFormat($sum->total_upfront_paid, 2, '.', '');
				$advisers[$sum->adviser_id]['total_ongoing_paid'] += self::numberFormat($sum->total_ongoing_paid, 2, '.', '');
				$advisers[$sum->adviser_id]['total_commission'] += self::numberFormat($sum->total_commission, 2, '.', '');

				foreach ($sum->branchSummaries as $br => $branchSummary) {
					if (!isset($branches[$branchSummary->branch_id])) {
						$branches[$branchSummary->branch_id] = [
							'branch' => $branchSummary->branch,
							'total_gst' => 0,
							'total_upfront_received' => 0,
							'total_ongoing_received' => 0,
							'total_upfront_paid' => 0,
							'total_ongoing_paid' => 0,
							'total_commission' => 0,

						];
					}

					$branches[$branchSummary->branch_id]['total_gst'] += self::numberFormat($branchSummary->total_gst, 2, '.', '');
					$branches[$branchSummary->branch_id]['total_upfront_received'] += self::numberFormat($branchSummary->total_upfront_received, 2, '.', '');
					$branches[$branchSummary->branch_id]['total_ongoing_received'] += self::numberFormat($branchSummary->total_ongoing_received, 2, '.', '');
					$branches[$branchSummary->branch_id]['total_upfront_paid'] += self::numberFormat($branchSummary->total_upfront_paid, 2, '.', '');
					$branches[$branchSummary->branch_id]['total_ongoing_paid'] += self::numberFormat($branchSummary->total_ongoing_paid, 2, '.', '');
					$branches[$branchSummary->branch_id]['total_commission'] += self::numberFormat($branchSummary->total_commission, 2, '.', '');

					$branchesTotal['total_gst'] += self::numberFormat($branchSummary->total_gst, 2, '.', '');
					$branchesTotal['total_upfront_received'] += self::numberFormat($branchSummary->total_upfront_received, 2, '.', '');
					$branchesTotal['total_ongoing_received'] += self::numberFormat($branchSummary->total_ongoing_received, 2, '.', '');
					$branchesTotal['total_upfront_paid'] += self::numberFormat($branchSummary->total_upfront_paid, 2, '.', '');
					$branchesTotal['total_ongoing_paid'] += self::numberFormat($branchSummary->total_ongoing_paid, 2, '.', '');
					$branchesTotal['total_commission'] += self::numberFormat($branchSummary->total_commission, 2, '.', '');

				}
			}

		}
		$feesDD['totalF'] = $total_fee;
		// return $feesDD;
		///dd($feesDD);
		/// dd([$total_take, $total_give, $feesDD]);
		$total_with_dist = $total_with_dist - $total_fee;
		//dd([$total_with_dist, $total_ng_forward, $total_fee, $total_with_dist - $total_fee]);
		return [

			'team' => $team,
			'branches' => $branches,
			'branchesTotal' => $branchesTotal,
			'advisers' => $advisers,
			'advisersTotal' => $advisersTotal,
			'total_gst' => $total_gst,
			'total_upfront_received' => $total_upfront_received,
			'total_ongoing_received' => $total_ongoing_received,
			'total_upfront_paid' => $total_upfront_paid,
			'total_ongoing_paid' => $total_ongoing_paid,
			'total_commission' => $total_commission,
			'total_fee' => $total_fee,
			'total_take' => $total_take,
			'total_give' => $total_give,
			'total_dist' => $total_dist,
			'total_with_dist' => $total_with_dist,
		];

	}

	public static function getDatesYTD() {
		$monthEnd = 6;
		$dayEnd = 30;
		$monthStart = 7;
		$dayStart = 1;

		$currentYear = \Carbon\Carbon::now()->year;
		$currentMonth = \Carbon\Carbon::now()->month;
		$currentDay = \Carbon\Carbon::now()->format('d');

		$start_date = '';
		$end_date = '';
		$startYear = '';
		$endYear = '';
		// if monthstart is geater than current month
		// example
		// july(7) > jan(1) - the start years should be the last year
		if ($monthStart > $currentMonth) {
			$startYear = $currentYear - 1;
			$endYear = $currentYear;
		} elseif ($monthStart == $currentMonth && $currentDay >= $dayStart) {
			$startYear = $currentYear;
			$endYear = $currentYear + 1;
		} else {
			$startYear = $currentYear;
			$endYear = $currentYear + 1;
		}

		$start_date = $startYear . '-' . '0' . $monthStart . '-' . '0' . $dayStart . ' 00:00:01';
		$end_date = $endYear . '-' . '0' . $monthEnd . '-' . $dayEnd . ' 23:59:59';
		return [$start_date, $end_date];
	}

	public static function processBranch($branchSummaries, &$branches) {
		foreach ($branchSummaries as $key => $branch) {

			if (!isset($branches[$branch->branch_id])) {
				$branches[$branch->branch_id] = [
					'name' => $branch->branch->name,
					'total_upfront_received' => 0,
					'total_upfront_paid' => 0,
					'total_ongoing_received' => 0,
					'total_ongoing_paid' => 0,
					'total_gst' => 0,
					'total_commission' => 0,
				];
			}

			$branches[$branch->branch_id]['total_upfront_received'] += $branch->total_upfront_received;
			$branches[$branch->branch_id]['total_upfront_paid'] += $branch->total_upfront_paid;
			$branches[$branch->branch_id]['total_ongoing_received'] += $branch->total_ongoing_received;
			$branches[$branch->branch_id]['total_ongoing_paid'] += $branch->total_ongoing_paid;
			$branches[$branch->branch_id]['total_gst'] += $branch->total_gst;
			$branches[$branch->branch_id]['total_commission'] += $branch->total_commission;
		}
	}

	public static function processCSVBank($data, $company) {

		$branches = \App\Branch::where('company_id', $company->id)->get();
		$branchData = [];
		foreach ($data as $key => $value) {

			foreach ($branches as $c => $b) {
				if (strtolower($b->name) == strtolower($value['supplier'])) {
					if (!isset($branchData[$b->hash_id])) {
						$branchData[$b->hash_id] = [
							'branch' => $b,
							'data' => [],
						];
					}
					$value['amount'] = self::numberFormat(floatval(str_replace([',', '$'], ['', ''], trim($value['amount']))));
					$branchData[$b->hash_id]['data'][] = $value;
				}
			}

		}

		return $branchData;
	}

	public static function SaveClawBack($running, $result, $prvClawback) {

		$clawbacks = \App\Clawback::where('running_commission_id', $running->id)->get();
		if (count($clawbacks) <= 0) {
			foreach ($result as $d) {
				$discre = 0;
				if ($d['summaries']['upfront_received'] != 0 || $d['summaries']['upfront_paid'] != 0 || $d['summaries']['ongoing_received'] != 0 || $d['summaries']['ongoing_paid'] != 0 || $d['summaries']['gst'] != 0) {

					$total = 0;

					foreach ($d['advisers'] as $k => $record) {
						if ($record['summaries']['upfront_received'] != 0 || $record['summaries']['upfront_paid'] != 0 || $record['summaries']['ongoing_received'] != 0 || $record['summaries']['ongoing_paid'] != 0 || $record['summaries']['gst'] != 0) {

							$total += $record['summaries']['total'];

						}
					}

					$discre = self::twoDecimalPlaces($total - $d['bank']);

				}

				$amount = 0;
				if (count($prvClawback) > 0) {
					foreach ($prvClawback as $c => $br) {
						if ($br->branch_id == $d['branch']->id) {
							$amount = $discre - $br->amount;
						}
					}
				} else {
					$amount = $discre;
				}
				$clawback = \App\Clawback::where('branch_id', $d['branch']->id)->where('running_commission_id', $running->id)->first();
				if (is_null($clawback)) {
					$clawback = new \App\Clawback;
					$clawback->branch_id = $d['branch']->id;
					$clawback->running_commission_id = $running->id;
					$clawback->amount = $amount;
					$clawback->save();
					$clawbacks[] = $clawback;
				}
			}
		}
		return $clawbacks;

	}

	public static function twoDecimalPlaces($value) {
		if (!is_numeric($value)) {
			return 0.00;
		}
		if ($value >= 0) {
			return number_format($value, 2, '.', '');
		}
		return number_format($value * -1, 2, '.', '');
	}
	public static function processFilter($filter, $com) {

		$com = $com->where(function ($query) use ($filter) {
			foreach ($filter as $k => $f) {
				self::checkFilter($f, $query, $k);
			}
		});
		return $com;
	}

	public static function sortBy($filter, $query) {
		foreach ($filter as $k => $f) {
			if ($f['name'] == 'sortedBy') {
				$query->orderBy($f['value'], $f['range']);
			}
		}
		return $query;
	}
	public static function checkFilter($filter, $query, $c) {

		switch ($filter['name']) {
		case 'adviser_name':
			if ($filter['operator'] == 'AND' || $c == 0) {
				$query->whereHas('adviser', function ($q) use ($filter) {
					$values = explode(',', $filter['value']);
					foreach ($values as $k => $v) {
						if ($k == 0) {
							$q->where('name', 'like', '%' . $v . '%');
						} else {
							$q->orWhere('name', 'like', '%' . $v . '%');
						}

					}

				});
			} else {

				$query->orWhereHas('adviser', function ($q) use ($filter) {
					$values = explode(',', $filter['value']);
					foreach ($values as $k => $v) {
						if ($k == 0) {
							$q->where('name', 'like', '%' . $v . '%');
						} else {
							$q->orWhere('name', 'like', '%' . $v . '%');
						}

					}
				});
			}
			break;
		case 'branch_name':
			if ($filter['operator'] == 'AND' || $c == 0) {
				$query->whereHas('branch', function ($q) use ($filter) {
					$values = explode(',', $filter['value']);
					foreach ($values as $k => $v) {
						if ($k == 0) {
							$q->where('name', 'like', '%' . $v . '%');
						} else {
							$q->orWhere('name', 'like', '%' . $v . '%');
						}

					}
				});
			} else {
				$query->orWhereHas('branch', function ($q) use ($filter) {
					$values = explode(',', $filter['value']);
					foreach ($values as $k => $v) {
						if ($k == 0) {
							$q->where('name', 'like', '%' . $v . '%');
						} else {
							$q->orWhere('name', 'like', '%' . $v . '%');
						}

					}
				});
			}

			break;
		case 'upfront_received':
			if ($filter['operator'] == 'AND' || $c == 0) {
				if ($filter['value'] == 'range') {
					$range = explode('-', $filter['range']);
					if (count($range) < 2) {
						$query->where('upfront_received', ">=", floatval($range[0]));
					} else {
						$query->where('upfront_received', ">=", floatval($range[0]))->where('upfront_received', "<=", floatval($range[1]));
					}
				} else {
					$range = explode('-', $filter['value']);
					if (count($range) < 2) {
						$new_v = explode(',', $filter['value']);
						$query->where('upfront_received', $new_v[1], floatval($new_v[0]));

					} else {

						$query->where('upfront_received', ">=", floatval($range[0]))->where('upfront_received', "<=", floatval($range[1]));
					}
				}

			} else {

				$query->orWhere(function ($q) use ($filter) {
					if ($filter['value'] == 'range') {
						$range = explode('-', $filter['range']);
						if (count($range) < 2) {
							$q->where('upfront_received', ">=", floatval($range[0]));
						} else {
							$q->where('upfront_received', ">=", floatval($range[0]))->where('upfront_received', "<=", floatval($range[1]));
						}
					} else {
						$range = explode('-', $filter['value']);
						if (count($range) < 2) {
							$new_v = explode(',', $filter['value']);
							$query->where('upfront_received', $new_v[1], floatval($new_v[0]));

						} else {

							$query->where('upfront_received', ">=", floatval($range[0]))->where('upfront_received', "<=", floatval($range[1]));
						}
					}
				});
			}
			break;

		case 'gst':
			if ($filter['operator'] == 'AND' || $c == 0) {
				if ($filter['value'] == 'range') {
					$range = explode('-', $filter['range']);
					if (count($range) < 2) {
						$query->where('gst', ">=", floatval($range[0]));
					} else {
						$query->where('gst', ">=", floatval($range[0]))->where('gst', "<=", floatval($range[1]));
					}
				} else {
					$range = explode('-', $filter['value']);
					if (count($range) < 2) {
						$new_v = explode(',', $filter['value']);
						$query->where('gst', $new_v[1], floatval($new_v[0]));

					} else {

						$query->where('gst', ">=", floatval($range[0]))->where('gst', "<=", floatval($range[1]));
					}
				}

			} else {

				$query->orWhere(function ($q) use ($filter) {
					if ($filter['value'] == 'range') {
						$range = explode('-', $filter['range']);
						if (count($range) < 2) {
							$q->where('gst', ">=", floatval($range[0]));
						} else {
							$q->where('gst', ">=", floatval($range[0]))->where('gst', "<=", floatval($range[1]));
						}
					} else {
						$range = explode('-', $filter['value']);
						if (count($range) < 2) {
							$new_v = explode(',', $filter['value']);
							$query->where('gst', $new_v[1], floatval($new_v[0]));

						} else {

							$query->where('gst', ">=", floatval($range[0]))->where('gst', "<=", floatval($range[1]));
						}
					}
				});
			}

			break;
		case 'ongoing_received':

			if ($filter['operator'] == 'AND' || $c == 0) {
				if ($filter['value'] == 'range') {
					$range = explode('-', $filter['range']);
					if (count($range) < 2) {
						$query->where('ongoing_received', ">=", floatval($range[0]));
					} else {
						$query->where('ongoing_received', ">=", floatval($range[0]))->where('ongoing_received', "<=", floatval($range[1]));
					}
				} else {
					$range = explode('-', $filter['value']);
					if (count($range) < 2) {
						$new_v = explode(',', $filter['value']);
						$query->where('ongoing_received', $new_v[1], floatval($new_v[0]));

					} else {

						$query->where('ongoing_received', ">=", floatval($range[0]))->where('ongoing_received', "<=", floatval($range[1]));
					}
				}

			} else {

				$query->orWhere(function ($q) use ($filter) {
					if ($filter['value'] == 'range') {
						$range = explode('-', $filter['range']);
						if (count($range) < 2) {
							$q->where('ongoing_received', ">=", floatval($range[0]));
						} else {
							$q->where('ongoing_received', ">=", floatval($range[0]))->where('ongoing_received', "<=", floatval($range[1]));
						}
					} else {
						$range = explode('-', $filter['value']);
						if (count($range) < 2) {
							$new_v = explode(',', $filter['value']);
							$query->where('ongoing_received', $new_v[1], floatval($new_v[0]));

						} else {

							$query->where('ongoing_received', ">=", floatval($range[0]))->where('ongoing_received', "<=", floatval($range[1]));
						}
					}
				});
			}

			break;
		case 'upfront_paid':
			if ($filter['operator'] == 'AND' || $c == 0) {
				$query->where('upfront_paid', $filter['value']);
			} else {
				$query->orWhere('upfront_paid', $filter['value']);
			}

			break;
		case 'ongoing_paid':
			if ($filter['operator'] == 'AND' || $c == 0) {
				$query->where('ongoing_paid', $filter['value']);
			} else {
				$query->orWhere('ongoing_paid', $filter['value']);
			}

			break;
		case 'client':
			if ($filter['operator'] == 'AND' || $c == 0) {
				//$query->where('client_name', 'like', '%' . $filter['value'] . '%');
				$query->where(function ($q) use ($filter) {
					$values = explode(',', $filter['value']);
					if (count($values) == 0 || empty($filter['value'])) {
						$q->whereNull('client_name')->orWhere('client_name', 'like', '%%');
					}
					foreach ($values as $k => $v) {
						if ($k == 0) {
							$q->where('client_name', 'like', '%' . $v . '%');
						} else {
							$q->orWhere('client_name', 'like', '%' . $v . '%');
						}

					}
				});
			} else {
				//$query->orWhere('client_name', 'like', '%' . $filter['value'] . '%');
				$query->orWhere(function ($q) use ($filter) {
					$values = explode(',', $filter['value']);
					if (count($values) == 0 || empty($filter['value'])) {
						$q->whereNull('client_name')->orWhere('client_name', 'like', '%%');
					}
					foreach ($values as $k => $v) {
						if ($k == 0) {
							$q->where('client_name', 'like', '%' . $v . '%');
						} else {
							$q->orWhere('client_name', 'like', '%' . $v . '%');
						}

					}
				});
			}

			break;
		case 'product_tag':
			if ($filter['operator'] == 'AND' || $c == 0) {
				$values = explode(',', $filter['value']);
				$product_tags_ids = \App\ProductTag::whereIn('name', $values)->get(['id'])->toArray();
				$product_ids = \App\Product::whereIn('id', $product_tags_ids)->get(['id'])->toArray();

				$query->whereIn('product_id', $product_ids);
			} else {
				$values = explode(',', $filter['value']);
				$product_tags_ids = \App\ProductTag::whereIn('name', $values)->get(['id'])->toArray();
				$product_ids = \App\Product::whereIn('id', $product_tags_ids)->get(['id'])->toArray();

				$query->orWhereIn('product_id', $product_ids);
			}

			break;
		case 'code':
			if ($filter['operator'] == 'AND' || $c == 0) {
				$query->where('product_code', 'like', '%' . $filter['value'] . '%');
			} else {
				$query->orWhere('product_code', 'like', '%' . $filter['value'] . '%');
			}

			break;
		case 'product':
			if ($filter['operator'] == 'AND' || $c == 0) {
				//$query->where('product', 'like', '%' . $filter['value'] . '%');
				$query->where(function ($q) use ($filter) {
					$values = explode(',', $filter['value']);
					if (count($values) == 0 || empty($filter['value'])) {
						$q->whereNull('product')->orWhere('product', 'like', '%%');
					}
					foreach ($values as $k => $v) {
						if ($k == 0) {
							$q->where('product', 'like', '%' . $v . '%');
						} else {
							$q->orWhere('product', 'like', '%' . $v . '%');
						}

					}
				});
			} else {
				//$query->orWhere('product', 'like', '%' . $filter['value'] . '%');
				$query->orWhere(function ($q) use ($filter) {
					$values = explode(',', $filter['value']);
					if (count($values) == 0 || empty($filter['value'])) {
						$q->whereNull('product')->orWhere('product', 'like', '%%');
					}
					foreach ($values as $k => $v) {
						if ($k == 0) {
							$q->where('product', 'like', '%' . $v . '%');
						} else {
							$q->orWhere('product', 'like', '%' . $v . '%');
						}

					}
				});
			}

			break;
		case 'date':
			if ($filter['value'] == 'range') {
				if ($filter['operator'] == 'AND' || $c == 0) {

					$date = explode(',', $filter['range']);
					if (count($date) > 1) {
						$dateRange = [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59'];
						$query->whereBetween('comm_edate', $dateRange);
					}

				} else {
					$date = explode(',', $filter['range']);
					if (count($date) > 1) {
						$dateRange = [$date[0] . ' 00:00:00', $date[1] . ' 23:59:59'];
						$query->orWhereBetween('comm_edate', $dateRange);
					}
				}
			} else {
				list($number_of_months, $ope) = explode(',', $filter['value']);

				$from_date = date('Y-m-1 00:00:00', strtotime("$ope$number_of_months months", strtotime(date('Y-m-d'))));

				$to_date = date('Y-m-d 23:59:59', strtotime('last day of this month', strtotime("-1 months", strtotime(date('Y-m-d')))));

				$dateRange = [$from_date, $to_date];
				//dd($dateRange);
				if ($filter['operator'] == 'AND' || $c == 0) {
					$query->whereBetween('comm_edate', $dateRange);
				} else {
					$query->orWhereBetween('comm_edate', $dateRange);
				}
			}

			break;
		default:
			# code...
			break;
		}
	}

	public static function recordSummary($commrecords) {
		$summaries = [];

		foreach ($commrecords as $key => $record) {
			if (!isset($summaries[$record->adviser_id])) {

				$summaries[$record->adviser_id] = [
					'adviser' => $record->adviser,
					'total_gst' => 0,
					'total_upfront_received' => 0,
					'total_ongoing_received' => 0,
					'total_upfront_paid' => 0,
					'total_ongoing_paid' => 0,
					'total' => 0,
				];
			}

			$summaries[$record->adviser_id]['total_gst'] += $record->gst;
			$summaries[$record->adviser_id]['total_upfront_received'] += $record->upfront_received;
			$summaries[$record->adviser_id]['total_ongoing_received'] += $record->ongoing_received;
			$summaries[$record->adviser_id]['total_upfront_paid'] += $record->upfront_paid;
			$summaries[$record->adviser_id]['total_ongoing_paid'] += $record->ongoing_paid;
			$summaries[$record->adviser_id]['total'] += $record->total_commission;
		}
		return $summaries;
	}

	public static function processReportNew($filter) {
		$query = \DB::table('running_commission_adviser_fee')->select('adviser_fees.fee_name', 'adviser_fees.created_at', 'adviser_fees.fee_amount', 'adviser_fees.action_type', 'advisers.name', 'running_commissions.name as commission')
			->join('adviser_fees', 'adviser_fees.id', '=', 'running_commission_adviser_fee.adviser_fee_id')
			->join('advisers', 'advisers.id', '=', 'adviser_fees.adviser_id')
			->join('running_adviser_summaries', 'running_adviser_summaries.id', '=', 'running_commission_adviser_fee.running_summary_id')
			->join('running_commissions', 'running_commissions.id', '=', 'running_adviser_summaries.running_commission_id')->where('running_commissions.status', '!=', 0);
		$query2 = \DB::table('running_commission_team_fee')->select('team_fees.fee_name', 'team_fees.created_at', 'team_fees.fee_amount', 'team_fees.action_type', 'teams.name', 'running_commissions.name as commission')
			->join('team_fees', 'team_fees.id', '=', 'running_commission_team_fee.team_fee_id')
			->join('teams', 'teams.id', '=', 'team_fees.team_id')
			->join('running_team_summaries', 'running_team_summaries.id', '=', 'running_commission_team_fee.running_summary_id')
			->join('running_commissions', 'running_commissions.id', '=', 'running_team_summaries.running_commission_id')->where('running_commissions.status', '!=', 0);

		if (count($filter) > 0) {

			$query = self::processFilterFee($filter, $query, 1);
			$query2 = self::processFilterFee($filter, $query2, 2);
		}
		$query = $query->unionAll($query2);

		return $query;
	}

	public static function processFilterFee($filter, $com, $type) {

		$com = $com->where(function ($query) use ($filter, $type) {
			foreach ($filter as $k => $f) {
				self::checkFilterFee($f, $query, $k, $type);
			}
		});
		return $com;
	}
	public static function processReportSummaryNew($filter) {
		$query = \DB::table('running_commission_adviser_fee')->select(DB::raw('sum(adviser_fees.fee_amount) as fee_amount'), 'adviser_fees.action_type as action_type', 'advisers.name')
			->join('adviser_fees', 'adviser_fees.id', '=', 'running_commission_adviser_fee.adviser_fee_id')
			->join('advisers', 'advisers.id', '=', 'adviser_fees.adviser_id')
			->join('running_adviser_summaries', 'running_adviser_summaries.id', '=', 'running_commission_adviser_fee.running_summary_id')
			->join('running_commissions', 'running_commissions.id', '=', 'running_adviser_summaries.running_commission_id')->where('running_commissions.status', '!=', 0);
		$query2 = \DB::table('running_commission_team_fee')->select(DB::raw('sum(team_fees.fee_amount)  as total_amount'), DB::raw('team_fees.action_type as act'), 'teams.name')
			->join('team_fees', 'team_fees.id', '=', 'running_commission_team_fee.team_fee_id')
			->join('teams', 'teams.id', '=', 'team_fees.team_id')
			->join('running_team_summaries', 'running_team_summaries.id', '=', 'running_commission_team_fee.running_summary_id')
			->join('running_commissions', 'running_commissions.id', '=', 'running_team_summaries.running_commission_id')->where('running_commissions.status', '!=', 0);

		if (count($filter) > 0) {

			$query = self::processFilterFee($filter, $query, 1);

			$query2 = self::processFilterFee($filter, $query2, 2);

		}

		$query = $query->groupBy('name', 'action_type');

		//dd($query->toSql());
		$query2 = $query2->groupBy('name', 'action_type');
		$query = $query->unionAll($query2);

		return $query;
	}

	private static function checkFilterFee($filter, $query, $c, $type) {

		switch ($filter['name']) {
		case 'adviser_id':
			if ($filter['operator'] == 'AND' || $c == 0) {
				$values = explode(',', $filter['value']);
				$adviser_ids = [];
				$teams_id = [];
				foreach ($values as $k => $v) {
					$id = explode('_', $v);
					$adviser_id = $id[1];

					if ($id[0] == 'ad') {
						$adviser_ids[] = $adviser_id;
					} else {
						$teams_id[] = $adviser_id;
					}

				}

				$query->where(function ($q) use ($adviser_ids, $teams_id, $type) {
					$wh = 'whereIn';
					if ($type == 1) {
						//if (count($adviser_ids) > 0) {
						$q->whereIn('advisers.id', $adviser_ids);
						$wh = 'orWhereIn';
						//}
					} else {
						//if (count($teams_id) > 0) {

						$q->{$wh}('teams.id', $teams_id);
						//}
					}

				});

			} else {

				$values = explode(',', $filter['value']);
				$adviser_ids = [];
				$teams_id = [];
				foreach ($values as $k => $v) {
					$id = explode('_', $v);
					$adviser_id = $id[1];

					if ($id[0] == 'ad') {
						$adviser_ids[] = $adviser_id;
					} else {
						$teams_id[] = $adviser_id;
					}

				}

				$query->orWhere(function ($q) use ($adviser_ids, $teams_id, $type) {
					$wh = 'whereIn';
					if ($type == 1) {
						if (count($adviser_ids) > 0) {
							$q->whereIn('advisers.id', $adviser_ids);
							$wh = 'orWhereIn';
						}
					} else {
						if (count($teams_id) > 0) {
							$q->{$wh}('teams.id', $teams_id);
						}
					}
				});

			}
			break;

		case 'commission':
			if ($filter['operator'] == 'AND' || $c == 0) {
				$values = explode(',', $filter['value']);

				$query->whereIn('running_commissions.id', $values);

			} else {

				$values = explode(',', $filter['value']);

				$query->orWhereIn('running_commissions.id', $values);

			}
			break;

		case 'action':
			if ($filter['operator'] == 'AND' || $c == 0) {

				$query->where('action_type', $filter['value']);

			} else {

				$values = explode(',', $filter['value']);

				$query->orWhere('action_type', $filter['value']);

			}
			break;

		case 'amount':
			if ($filter['operator'] == 'AND' || $c == 0) {
				if ($filter['value'] == 'range') {
					$range = explode('-', $filter['range']);
					if (count($range) < 2) {
						$query->where('fee_amount', ">=", floatval($range[0]));
					} else {
						$query->where('fee_amount', ">=", floatval($range[0]))->where('fee_amount', "<=", floatval($range[1]));
					}
				} else {
					$range = explode('-', $filter['value']);
					if (count($range) < 2) {
						$new_v = explode(',', $filter['value']);
						$query->where('fee_amount', $new_v[1], floatval($new_v[0]));

					} else {

						$query->where('fee_amount', ">=", floatval($range[0]))->where('fee_amount', "<=", floatval($range[1]));
					}
				}

			} else {

				$query->orWhere(function ($q) use ($filter) {
					if ($filter['value'] == 'range') {
						$range = explode('-', $filter['range']);
						if (count($range) < 2) {
							$q->where('fee_amount', ">=", floatval($range[0]));
						} else {
							$q->where('fee_amount', ">=", floatval($range[0]))->where('fee_amount', "<=", floatval($range[1]));
						}
					} else {
						$range = explode('-', $filter['value']);
						if (count($range) < 2) {
							$new_v = explode(',', $filter['value']);
							$query->where('fee_amount', $new_v[1], floatval($new_v[0]));

						} else {

							$query->where('fee_amount', ">=", floatval($range[0]))->where('fee_amount', "<=", floatval($range[1]));
						}
					}
				});
			}

			break;
		}
	}
}
