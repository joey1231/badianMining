<template>
	<div class="row">
		<div class="col-md-12">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							View Sales
						</h3>
					</div>
					
				</div>

			
				<div class="kt-portlet__body">
					
						<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
							<div class="row align-items-center">
								<div class="col-md-12">
									<div class="form-group">
										<label>Sale Name</label>
										
										<input type="text" class="form-control" aria-describedby="emailHelp" v-model="model.name"  placeholder="name" disabled>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<label>Sale Price</label>
										
										<input  type="number" class="form-control" aria-describedby="emailHelp" v-model="model.sale_price"  placeholder="Sale Price" disabled>
									</div>
								</div>
								
								
									
							  	
								<div class="col-md-12 " style="margin-top:20px;margin-bottom:20px">
									<div class="card" >
									 
									  <div class="card-body">
									  		<h3>Sales Item</h3>
											<table class="table table-sm table-head-bg-brand"
											v-loading="loading">
												<thead class="thead-inverse">
													<tr>
														
														<th width="400">Date</th>
														<th width="400">KG</th>
														
														
													</tr>
												</thead>
												<tbody>
														
														<tr v-for="(i, index)  in items">
															
															<td>{{i.item_date }}</td>
															
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
											  		<h3>Sales Computation</h3>
													<table class="table table-sm table-head-bg-brand">
														<thead class="thead-inverse">
																<tr>
																	<th>Total KG/TONS</th>
																	<th>{{model.total_kg | NumberFormat}}</th>
																</tr>

																<tr>
																	<th>Price</th>
																	<th>{{model.sale_price | NumberFormat}}</th>
																</tr>

																<tr>
																	<th>Total Sales Amount</th>
																	<th>{{model.total_amount | NumberFormat}}</th>
																</tr>

																<tr>
																	<th>Total Expenses</th>
																	<th>{{model.total_expences | NumberFormat}}</th>
																</tr>

																<tr>
																	<th>Net Total Amount</th>
																	<th>{{model.total_net_amount | NumberFormat}}</th>
																</tr>
														</thead>
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
	import saleService from './../../services/sale/index.js';
	
	import { debounce } from "debounce";
	export default {
		data(){
			return {
				models: [],
				advisers:[],
				items:[],
				expenses:[],
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
			Promise.all([saleService.info(this.hash_id)]).then((data) => {
				
				me.model = data[0].data.data;
				console.log(me.model);
				me.items = me.model.items;
				me.expenses = me.model.expenses;
				
				
			}).catch((data)=> {
				console.log(data);
			});
			
		}
	}

</script>