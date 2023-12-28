<template>
	<div class="row">
		<div class="col-md-12">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							View Sharing
						</h3>
					</div>
					
				</div>

			
				<div class="kt-portlet__body">
					
						<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
							<div class="row align-items-center">
								<div class="col-md-12">
									<div class="form-group">
										<label>Share Name</label>
										
										<input type="text" class="form-control" aria-describedby="emailHelp" v-model="model.name"  placeholder="name" disabled>
									</div>
								</div>
								
								
									
							  	
								<div class="col-md-12 " style="margin-top:20px;margin-bottom:20px">
									<div class="card" >
									 
									  <div class="card-body">
									  		<h3> Items</h3>
											<table class="table table-sm table-head-bg-brand"
											v-loading="loading">
													<thead class="thead-inverse">
													<tr>
														
														<th width="400">Sale</th>
														<th width="400">Created Date</th>
														
														<th width="400">Total Amount</th>
														<th width="400">Total Tons</th>
														
													</tr>
												</thead>
												<tbody>
													
														<tr v-for="(i, index)  in items">
															
															<td>{{i.sale.name }}</td>
															<td>{{i.sale.created_at | DateFormat }}</td>
															<td>{{i.total_amount | NumberFormat}}</td>
															<td>{{i.total_kg | NumberFormat}}</td>
														
															
														</tr>
														
												</tbody>
											</table>

											</div>
									</div>
										</div>


										<div class="col-md-12 " style="margin-top:20px;margin-bottom:20px">
											<div class="card" >
											 
											  <div class="card-body">
											  		<h3>Expenses Item</h3>
													<table class="table table-sm table-head-bg-brand"
													v-loading="loading">
														<thead class="thead-inverse">
															<tr>
																
																<th width="400">Name</th>
																<th width="400">Amount</th>
																
																
															</tr>
														</thead>
														<tbody>
																
																<tr v-for="(i, index)  in expenses">
																	
																	<td><p><b>{{i.name}}</b></p>
																		
																	</td>

																	<td>{{i.total_amount | NumberFormat}}</td>
																	
																
																	
																</tr>
																
														</tbody>
													</table>

													</div>
											</div>
										</div>
									

									<div class="col-md-12 " style="margin-top:20px;margin-bottom:20px">
											<div class="card" >
											 
											  <div class="card-body">
											  		<h3>Investors And Owners Shares</h3>
													<table class="table table-sm table-head-bg-brand"
													v-loading="loading">
														<thead class="thead-inverse">
															<tr>
																
																<th width="400">Name</th>
																<th width="400">Status</th>
																<th width="400">Share Type</th>
																<th width="400">Share Value</th>
																<th width="400">Total Amount</th>

																<th width="400">Action</th>
															</tr>
														</thead>
														<tbody>
																
																<tr v-for="(i, index)  in shared">
																	
																	<td><p><b>{{i.user.name}}</b></p>
																	<td><p><b>{{i.status | capitalize}}</b></p>	
																	</td>

																	<td>{{i.share_type | capitalize}}</td>
																	
																	<td>{{i.share_value | NumberFormat}}</td>
																	<td>{{i.amount | NumberFormat}}</td>
																	<td> <button class="btn btn-success" @click="sharePaid(i.hash_id,index)" v-if="i.status == 'pending'"><i class="fa fa-check"></i></button> </td>
																</tr>
																
														</tbody>
													</table>
													<hr/>
													
													</div>
												</div>
											</div>
										</div>

								   	
										</div>
							</div>
						</div>
				
				</div>
				
				
			</div>
			
			</div>
		
		
		</div>
		
	
</template>
<style>
.el-select{
	width:100%;
}
</style>
<script type="text/javascript">
	import {mapGetters} from 'vuex';
import shareService from './../../services/share/index.js';
	
	import { debounce } from "debounce";
	export default {
		data(){
			return {
				models: [],
				advisers:[],
				items:[],
				expenses:[],
				shared:[],
				dialogVisible:false,
				dialogVisibleLogo:false,
				filter:{
					adviser_name:'',
					client:'',
				},
				model:{
					sale_price:0,
					invoice_price:0,
					name:'',
					total_kg:0,
					total_amount:0,
					total_net_amount:0,
					total_expences:0,
					total_invoice_price_e_vat:0,
					total_invoice_price_w_vat:0,
					total_invoice_vat:0,
					total_invoice_wh_tax:0,
					total_invoice_amount:0,
					total_invoice_amount_due_w_wh_tax:0,
					total_with_devided_1_12:0,
					items:[],
					expenses:[],
				},
				loading:false,
				loadingclient:false,
				loadinglogo:false,
				item:{
					rr_no:'',
					ctp_no:'',
					total_kg:'',
					date:'',
					
				},
				expense:{
					name:'',
					amount:'',
				},
				client:{},
				clients:[],
				selected_client:{
					email:'',
					value:0,
				},
				logo:{
					file:'',
					name:'',
					address:'',
					rep_no:'',
					mobile:'',
					phone:'',
					website:'',
				},
				logos:[],
				selected_logo:{},

			}
		},
		watch: {
			
		},
		methods: {
			
			
			sharePaid(hash_id,i) {
				let me = this;
				 me.$confirm('This will permanently change status to Paid. Continue?', 'Warning', {
			          confirmButtonText: 'OK',
			          cancelButtonText: 'Cancel',
			          type: 'warning'
			    }).then(() => {
			        shareService.sharedPaid(hash_id).then(function(response){  
						me.shared[i].status='Paid';
				    }).catch(function(response){
				           console.log(response)
				    });
			    }).catch(() => {
			                    
			    });
			},

		},
		computed: {
	      ...mapGetters({
	         imgSrc: 'getUploadPath',
	         importObject: 'getImportObject',
	      }),
	    },
		props: {
			admin: {
				type: String,
				required: true,
			},
			
			hash_id: {
				type: String,
				required: true,
			}
		},
		mounted(){
			
			
			let me = this;
			Promise.all([shareService.info(this.hash_id)]).then((data) => {
				
				me.model = data[0].data.data;
				console.log(me.model);
				me.items = me.model.items;
				me.expenses = me.model.expenses;
				
				me.shared = me.model.shares;
			}).catch((data)=> {
				console.log(data);
			});
			
		}
	}

</script>