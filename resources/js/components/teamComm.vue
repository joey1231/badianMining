<template>
	<div>
		<div class="col-md-12" v-for="(team,key) in teams"  >
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							{{team.team.name  }}
						</h3>
														
					</div>
					<div class="row" >
						<div class="col-md-12">
							<button class="btn btn-info" style="float:left;margin-top:15px" @click="download('team', team.team.id)"> Download Supplier Summary</button>
						</div>
					</div>
				</div>
				<div class="kt-portlet__body kt-portlet__body--fit">
				 <div class="kt-form kt-form--label-right kt-margin-t-20  kt-margin-l-20 kt-margin-r-20 kt-margin-b-10">

				 	<el-tabs :key="key">
			<el-tab-pane :label="team.team.name" name="teamInfo">
				<table class="table table-sm table-head-bg-brand">
												
					<tbody>
													
					<tr>
						<td  width="300">Upfront Received</td>
						<td width="300"  style="text-align:right;">{{team.summary.total_upfront_received | money }}</td>
					</tr>
					<tr>
						<td  width="300">Upfront Paid</td>
						<td width="300"  style="text-align:right;">{{team.summary.total_upfront_paid | money }}</td>
					</tr>
					<tr>
						<td  width="300">Ongoing Received</td>
						<td width="300"  style="text-align:right;">{{team.summary.total_ongoing_received | money }}</td>
					</tr>
					<tr>
						<td  width="300">Ongoing Paid</td>
						<td width="300"  style="text-align:right;">{{team.summary.total_ongoing_paid | money }}</td>
					</tr>
					<tr>
						<td  width="300">GST</td>
						<td width="300"  style="text-align:right;">{{team.summary.total_gst | money }}</td>
					</tr>
					<tr>
						<td  width="300"><b>Sub-total</b></td>
						<td width="300"  style="text-align:right;"><b>{{team.summary.total_commission | money }} </b></td>
					</tr>

					<tr>
						<td  width="300"><b>Fees:</b></td>
						<td width="300"></td>
					</tr>

					<tr v-for="fee in  feesGive(team.fees)">
						<td width="300"  style="padding-left:20px">{{fee.fee_name}}</td>
						<td width="300"  style="text-align:right;">{{fee.fee_amount | money }}</td>
					</tr>
					<tr>
						<td width="300"><b>Give Total:</b></td>
						<td width="300"  style="text-align:right;">{{team.summary.total_give | money }}</td>
					</tr>
					<tr v-for="fee in  feesTake(team.fees)">
						<td width="300" style="padding-left:20px">{{fee.fee_name}}</td>
						<td width="300"  style="text-align:right;">-{{fee.fee_amount | money }}</td>
					</tr>
					<tr>
						<td  width="300"><b>Take Total:</b></td>
						<td width="300"  style="text-align:right;">-{{team.summary.total_take | money }}</td>
					</tr>

					<tr>
						<td  width="300"><b>Total</b></td>
						<td width="300" style="text-align:right;"><b>{{( team.summary.total_commission + team.summary.total_fee ) | money }}</b></td>
					</tr>

					<tr>
						<td  width="300"><b>Total with distribution</b></td>
						<td width="300" style="text-align:right;"><b>{{( team.summary.total_adviser_dist + team.summary.total_fee ) | money }}</b></td>
					</tr>
				</tbody>
			</table>		
			</el-tab-pane>
			<el-tab-pane label="Adviser summary" name="teamAdviserrecordSummary">
				<table class="table table-sm table-head-bg-brand">
					<thead class="thead-inverse">
											
						<tr>
												
								<th width="200">Adviser Name</th>
								<th width="100">GST</th>
								<th width="100">Upfront Received</th>
								<th  width="100">Ongoing Received</th>
								<th  width="100">Upfront Paid</th>
								<th  width="100">Ongoing Paid</th>
								<th  width="100">Total</th>
												
						</tr>
					</thead>
						<tbody>
							<tr v-for="record in team.advisers">
													
								<td>{{record.adviser.name | capitalize}}</td>
								
								<td>{{record.summary.total_gst | money}}</td>
								<td>{{record.summary.total_upfront_received | money}}</td>
								<td>{{record.summary.total_ongoing_received | money}}</td>
								<td>{{record.summary.total_upfront_paid | money}}</td>
								<td>{{record.summary.total_ongoing_paid | money}}</td>
								<td>{{record.summary.total_commission  | money}}</td>
													
							</tr>				
						</tbody>
					</table>
			</el-tab-pane>

			<el-tab-pane label="Adviser Branch summary" name="teamAdviserBranchSummary">
			<div class="row"  v-for="record in team.advisers">
				<div class="col-md-12">
					<h1>{{record.adviser.name | capitalize}} </h1>
				</div>
				<div class='col-md-12'>
				  <table class="table table-sm table-head-bg-brand">
					<thead class="thead-inverse">
																										
						<tr>
																											
																										
							<th width="400">Branch Name</th>
							<th  width="100">Upfront Received</th>
							<th  width="100">Upfront Paid</th>
							<th  width="100">Ongoing Received</th>
							<th  width="100">Ongoing Paid</th>
							<th  width="100">GST</th>
							<th  width="100">Total</th>
						</tr>
						</thead>
						<tbody>
							<tr v-for="com in record.summaryBranches">
								<td>{{com.branch.name}}</td>
								<td>{{com.total_upfront_received | money}}</td>
								<td>{{com.total_upfront_paid | money}}</td>
								<td>{{com.total_ongoing_received | money}}</td>
								<td>{{com.total_ongoing_paid | money}}</td>
								<td>{{com.total_gst | money}}</td>
								<td>{{com.total_commission | money}}</td>
							</tr>

							<tr >{{  record.summary. total_commission| money}}</tr> 
							<tr>
								<td colspan="7"><b>Fees:</b></td>
																			
							</tr>
							<tr  v-for="com in record.fees.fees">
								<td colspan="6"><b>{{com.fee_name}}</b> ({{com.action_type}})</td>
																							
								<td>{{com.fee_amount | money}}</td>
																											
							</tr>

							<tr  v-for="com in record.fees.adviser">
								<td colspan="6"><b>{{com.fee_name}}</b> ({{com.action_type}})</td>
																											
								<td>{{com.fee_amount | money}}</td>
																											
							</tr>
																										
							<tr>
								<td colspan="7">&nbsp;</td>
																										
							</tr>
							<tr>
								<td colspan="6"><b>Total</b></td>
																											
								<td v-html="finalTotalDistri(record.summary.total_fee,record.summary.total_commission)"></td>
																											
							</tr>

							<tr>
								<td colspan="6"><b>{{record.summary.label_dist}}</b></td>
																											
								<td > {{-record.summary.total_company_dist | money}}</td>
																											
							</tr>
							<tr>
								<td colspan="6"><b> Total Distribution</b></td>
								<td v-html="finalTotalDistri(record.summary.total_fee,record.summary.total_adviser_dist)"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			</el-tab-pane>
			<el-tab-pane label="Adviser record" name="teamAdviserrecord">
				<table class="table table-sm table-head-bg-brand">
					<thead class="thead-inverse">
											
						<tr>
												
							<th width="100">Branch Name</th>
						    <th  width="100">Client Name</th>
							<th  width="100">Product</th>
							<th  width="100">Product Code</th>
							<th  width="100">Upfront Received</th>
							<th  width="100">Upfront Paid</th>
							<th  width="100">Ongoing Received</th>
							<th  width="100">Ongoing Paid</th>	
							<th  width="100">GST</th>
							<th  width="100">Total</th>				
						</tr>
					</thead>
					  
						<tbody  v-for="record in team.advisers">
							<tr v-for="r in record.record">
								<td>{{r.branch.name}}</td>
								<td>{{r.client_name}}</td>
								<td>{{r.product}}</td>	
								<td>{{r.product_code}}</td>
								<td>{{r.upfront_received | money}}</td>	
								<td>{{r.upfront_paid | money}}</td>	
								<td>{{r.ongoing_received | money}}</td>	
								<td>{{r.ongoing_paid | money}}</td>
								<td>{{r.gst | money}}</td>	
								<td>{{r.total_commission | money}}</td>
							</tr>				
						</tbody>
					
					</table>
			</el-tab-pane>
		</el-tabs>
				 </div>
				</div>
			</div>

		</div>
		
	</div>
</template>
<script>
	export default{
		name: 'TeamComponent',
		props:{
	      teams: {
	        type: [Array, Object],
	        required: true
	      },
	      download: {
	      	type: Function,
	      	required: true
	      }
	    },
	    computed: {

	    },
	    methods: {
	    	finalTotal(fee, comms){
				let total = 0;

               	Object.keys(comms).forEach(function(i) {
					
                	total += parseFloat(comms[i].summaries.total);

				});
               	let val = (total + fee).toFixed(4);

               	if(val >=0){
               		 return "<b>$"+val+"</b>"
               	}else{
               		return "<b>-$"+(val * -1).toFixed(2) +"<b/>";
               	}
               

			},

			finalTotalDistri(fee, total) {
				let val = (total + fee).toFixed(2);

               	if(val >=0){
               		 return "<b>$"+val+"</b>"
               	}else{
               		return "<b>-$"+(val * -1).toFixed(2) +"<b/>";
               	}
			},

			feesGive(fees){
	    		let feesGive = [];
	    		for(var i=0; i < fees.fees.length; i++) {
	    			let fee = fees.fees[i];

	    			if(fee.action_type == 'Give') {
	    				feesGive.push(fee);
	    			}
	    		}

	    		for(var i=0; i < fees.adviser.length; i++) {
	    			let fee = fees.adviser[i];

	    			if(fee.action_type == 'Give') {
	    				feesGive.push(fee);
	    			}
	    		}
	    		return feesGive;
	    	},

	    	feesTake(fees){
	    		let feesTake= [];

	    		for(var i=0; i < fees.fees.length; i++) {
	    			let fee = fees.fees[i];

	    			if(fee.action_type == 'Take') {
	    				feesTake.push(fee);
	    			}
	    		}

	    		for(var i=0; i < fees.adviser.length; i++) {
	    			let fee = fees.adviser[i];

	    			if(fee.action_type == 'Take') {
	    				feesTake.push(fee);
	    			}
	    		}
	    		return feesTake;
	    	},
	    }
	}
</script> 