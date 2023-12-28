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
				 	<el-tabs  :key="key">
			<el-tab-pane :label="team.team.name" name="teamInfo">
				<table class="table table-sm table-head-bg-brand">
												
					<tbody>
													
					<tr>
						<td  width="300">Upfront Received</td>
						<td width="300"  style="text-align:right;">{{team.recordTotal.upfront_received | money }}</td>
					</tr>
					<tr>
						<td  width="300">Upfront Paid</td>
						<td width="300"  style="text-align:right;">{{team.recordTotal.upfront_paid | money }}</td>
					</tr>
					<tr>
						<td  width="300">Ongoing Received</td>
						<td width="300"  style="text-align:right;">{{team.recordTotal.ongoing_received | money }}</td>
					</tr>
					<tr>
						<td  width="300">Ongoing Paid</td>
						<td width="300"  style="text-align:right;">{{team.recordTotal.ongoing_paid | money }}</td>
					</tr>
					<tr>
						<td  width="300">GST</td>
						<td width="300"  style="text-align:right;">{{team.recordTotal.gst | money }}</td>
					</tr>
					<tr>
						<td  width="300"><b>Sub-total</b></td>
						<td width="300"  style="text-align:right;"><b>{{team.recordTotal.total | money }} </b></td>
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
						<td width="300"  style="text-align:right;">{{team.fees.summaries.give | money }}</td>
					</tr>
					<tr v-for="fee in  feesTake(team.fees)">
						<td width="300" style="padding-left:20px">{{fee.fee_name}}</td>
						<td width="300"  style="text-align:right;">-{{fee.fee_amount | money }}</td>
					</tr>
					<tr>
						<td  width="300"><b>Take Total:</b></td>
						<td width="300"  style="text-align:right;">-{{team.fees.summaries.take | money }}</td>
					</tr>

					<tr>
						<td  width="300"><b>Total</b></td>
						<td width="300" style="text-align:right;"><b>{{( team.recordTotal.total + team.fees.summaries.total ) | money }}</b></td>
					</tr>

					<tr>
						<td  width="300"><b>Total with distribution</b></td>
						<td width="300" style="text-align:right;"><b>{{( team.total_dist.computed_adviser_total + team.fees.summaries.total ) | money }}</b></td>
					</tr>
				</tbody>
			</table>		
			</el-tab-pane>
			<el-tab-pane label="Team Adviser record Summary" name="teamAdviserrecordSummary">
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
						<tbody >
							<tr v-for="record in team.record">
													
								<td>{{record.adviser.name | capitalize}}</td>
								
								<td>{{record.recordTotal.gst | money}}</td>
								<td>{{record.recordTotal.upfront_received | money}}</td>
								<td>{{record.recordTotal.ongoing_received | money}}</td>
								<td>{{record.recordTotal.upfront_paid | money}}</td>
								<td>{{record.recordTotal.ongoing_paid | money}}</td>
								<td>{{record.recordTotal.total  | money}}</td>
													
							</tr>				
						</tbody>
					</table>
			</el-tab-pane>

			<el-tab-pane label="Adviser Summaries" name="teamAdviserrecord">
				<div class="row"  v-for="record in team.record">
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
							<tr v-for="com in record.record">
								<td>{{com.branch.name}}</td>
								<td>{{com.summaries.upfront_received | money}}</td>
								<td>{{com.summaries.upfront_paid | money}}</td>
								<td>{{com.summaries.ongoing_received | money}}</td>
								<td>{{com.summaries.ongoing_paid | money}}</td>
								<td>{{com.summaries.gst | money}}</td>
								<td>{{com.summaries.total | money}}</td>

							</tr>

							<tr v-html="processTotal(record.record)"></tr> 
							<tr>
								<td colspan="7"><b>Fees:</b></td>
							</tr>
							<tr v-for="com in record.fees.fees">
								
							</tr>
							<tr>
								<td colspan="7">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="6"><b>Total</b></td>
								<td v-html="finalTotal(0,record.record)"></td>
							</tr>
							<tr>
								<td colspan="6"><b>{{record.total_dist.label}}</b></td>
								<td > {{-record.total_dist.company_dist_value | money}}</td>
							<tr>
							<tr>
								<td colspan="6"><b> Total Distribution</b></td>
								<td v-html="finalTotal(0,record.record)"></td>
							</tr>			
						</tbody>
					</table>
				</div>
			</div>
			</el-tab-pane>
			<el-tab-pane label="Adviser record" name="teamSummaryBranchrecord">
				<table class="table table-sm table-head-bg-brand"  v-for="record in team.record">
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
					  
						<tbody  v-for="com in record.record">
							<tr v-for="r in com.records">
								<td>{{com.branch.name}}</td>
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
	    methods: {
			summarryName(index) {
				return 'summary_'+index
			},
			recordName(index) {
				return 'record_'+index
			},
			processTotal(comms){
				let upfront_received = 0,
                upfront_paid = 0,
                ongoing_received = 0,
                ongoing_paid = 0,
                total = 0,
                gst = 0;

               	Object.keys(comms).forEach(function(i) {

					upfront_received += parseFloat(comms[i].summaries.upfront_received);
                	upfront_paid += parseFloat(comms[i].summaries.upfront_paid);
                	ongoing_received += parseFloat(comms[i].summaries.ongoing_received);
                	ongoing_paid += parseFloat(comms[i].summaries.ongoing_paid);
                	gst += parseFloat(comms[i].summaries.gst);
                	total += parseFloat(comms[i].summaries.total);

				});
                	

                
				let trHtml = '<td><b>Sub-Total</b></td>';
				trHtml+=`<td><b>${upfront_received.toFixed(2)} </b></td>`;
				trHtml+=`<td><b>${upfront_paid.toFixed(2)} </b></td>`;
				trHtml+=`<td><b>${ongoing_received.toFixed(2)}</b></td>`;
				trHtml+=`<td><b>${ongoing_paid.toFixed(2)} </b></td>`;
				trHtml+=`<td><b>${gst.toFixed(2)}</b> </td>`;
				trHtml+=`<td><b>${total.toFixed(2)}</b> </td>`;

				return trHtml;
			},
			finalTotal(fee, comms){
				console.log(comms);
				let total = 0;

               	Object.keys(comms).forEach(function(i) {
					
                	total += parseFloat(comms[i].summaries.total);

				});
               	let val = (total + fee).toFixed(2);

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

	    		
	    		return feesTake;
	    	},
			

		},
	}
</script> 