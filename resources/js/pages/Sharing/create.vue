<template>
	<div class="row">
		<div class="col-md-12">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Sharing Sales
						</h3>
					</div>
					
				</div>

			
				<div class="kt-portlet__body">
					
						<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
							<div class="row align-items-center">
								<div class="col-md-12">
									<div class="form-group">
										<label>Sharing Name</label>
										
										<input type="text" class="form-control" aria-describedby="emailHelp" v-model="model.name"  placeholder="name">
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
														
														<th width="400">Sale</th>
														<th width="400">Created Date</th>
														<th width="400">Sale Price</th>

														<th width="400">Total Tons</th>
														<th width="400">Total Amount</th>
														<th width="400">Total Expences</th>
														
														<th width="400">Total Net Amount</th>
														<th width="200" style="text-align:right">Action</th>
													</tr>
												</thead>
												<tbody>
													
														<tr v-for="(i, index)  in items">
															
															<td>{{i.name }}</td>
															<td>{{i.created_at | DateFormat}}</td>
															<td>{{i.sale_price | NumberFormat}}</td>
															<td>{{i.total_kg | NumberFormat}}</td>
															<td>{{i.total_amount | NumberFormat}}</td>
															<td>{{i.total_expences | NumberFormat}}</td>
															
															<td>{{i.total_net_amount | NumberFormat}}</td>
															<td style="text-align:right"> <button class="btn btn-danger" @click="deleteField(index)" ><i class="flaticon-delete"></i></button> </td>
														</tr>
														<tr >
															
															<td class="total" colspan="2"><b>Total: </b></td>
															
															<td><b>{{total_sale_price | NumberFormat}}</b></td>
															<td><b>{{total_total_kg | NumberFormat}}</b></td>
															<td><b>{{total_total_amount | NumberFormat}}</b></td>
															<td><b>{{total_total_expences | NumberFormat}}</b></td>
															
														
															<td><b>{{total_total_net_amount | NumberFormat}}</b></td>
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
																
																<th width="200" style="text-align:right">Action</th>
															</tr>
														</thead>
														<tbody>
																<tr>
																	<td>
																		<div class="form-group">
																			
																			<input type="text" class="form-control" aria-describedby="emailHelp" v-model="expense.name"  >
																			
																		</div>
																		
																	</td>
																	

																	<td>
																		<div class="form-group">
																			
																			<input type="text" class="form-control" aria-describedby="emailHelp" v-model="expense.total_amount"  >
																		</div>
																	</td>
																	
																	<td style="text-align:right">
																		<button class="btn btn-primary" @click="addItemExpence"><i class="flaticon2-plus"></i></button>
																	</td>
																</tr>
																<tr v-for="(i, index)  in expenses">
																	
																	<td><p><b>{{i.name}}</b></p>
																		
																	</td>

																	<td>{{i.total_amount | NumberFormat}}</td>
																	
																
																	<td style="text-align:right"> <button class="btn btn-danger" @click="deleteFieldExpense(index)" ><i class="flaticon-delete"></i></button> </td>
																</tr>

																<tr >
															
																<td class="total"><b>Total: </b></td>
																
																<td><b>{{model.total_expences  | NumberFormat}}</b></td>
															
														</tr>
																
														</tbody>
													</table>

													</div>
											</div>
										</div>
									

									
										</div>

								   
										</div>
							</div>
						</div>
				
				</div>
				
				<div class="kt-portlet__foot">
						<div class="kt-form__actions">
							<button type="reset" class="btn btn-info" @click="save">Save</button>
							
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
.total{
	text-align: right;
}
</style>
<script type="text/javascript">
	import {mapGetters} from 'vuex';

	
	import saleService from './../../services/sale/index.js';
	import shareService from './../../services/share/index.js';

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
				selected_logo:{},
				total_sale_price:0,
				total_total_kg:0,
				total_total_amount:0,
				total_total_expences:0,
				total_total_net_amount:0,
			}
		},
		watch: {
			
		},
		methods: {
			
			searchData: debounce(function() {
				var me = this;
				me.models = [];
				me.summaries = [];
				me.dataFetch(me.pagenation.currentPage);
				me.currentChangeSummary(me.pagenationSummary.currentPage);
			},400),

			handleClose(done) {
		        this.$confirm('Are you sure you would like to cancel this action?')
		          .then(_ => {
		            done();
		          })
		          .catch(_ => {});
		      },

		    save() {
				let me = this;
			
				if(me.items.length == 0) {
					me.$message.error('Oops, We cant save this, items is empty.');
					return;
				}
				me.model.items = me.items;
				me.model.expenses = me.expenses;
				
				shareService.store(me.model).then(function(data) {
					location.href = `/shares/show/${data.data.data.hash_id}`;
				}).catch(function(data){
					me.$message.error(data.data.data.message);
				})
			},

			
			
			addItemExpence(){
				if(this.expense.name == ''){
					this.$confirm('name is empty')
		          .then(_ => {
		            done();
		          })
		          .catch(_ => {});
		          return;
				}
				if(this.expense.total_amount == ''){
					this.$confirm('Amount is empty')
		          .then(_ => {
		            done();
		          })
		          .catch(_ => {});
		          return;
				}
				this.expenses.push(this.expense);
				this.expense = {
					name:'',
					total_amount:'',
					
				}

				this.calculateSales();
			},
			addItem(){
				
				if(this.item.total_kg == ''){
					this.$confirm('Tons  is empty')
		          .then(_ => {
		            done();
		          })
		          .catch(_ => {});
		          return;
				}
				if(this.item.item_date == ''){
					this.$confirm('Date  is empty')
		          .then(_ => {
		            done();
		          })
		          .catch(_ => {});
		          return;
				}
				this.item.item_date = moment(this.item.item_date ).format('YYYY-MM-DD');
				this.items.push(this.item);
				this.item = {
					
					total_kg:'',
					item_date:'',
				}

				this.calculateSales();
			},

			

			
			calculateSales(){
				
				
				var total_kg = 0;
				var total_amount = 0 
				$.each( this.items, function( key, value ) {
				  	total_kg += parseFloat(value.total_kg);
				  	total_amount += parseFloat(value.total_net_amount);
				});

				

				var total_expences = 0;

				$.each(this.expenses, function( key, value ) {
				  	total_expences += parseFloat(value.total_amount);
				});
				var total_net = total_amount - total_expences;

				this.model.total_kg = total_kg;
				this.model.total_amount = total_amount;
				this.model.total_net_amount = total_net;
				this.model.total_expences = total_expences;
			},

			calculateInvoice(){
				if(this.model.invoice_price == '' ){
					this.$confirm('Invoice price is empty')
		          .then(_ => {
		            done();
		          })
		          .catch(_ => {});
		          return;
				}
				if(this.items.length ==0 ){
					this.$confirm('Sales item is empty')
		          .then(_ => {
		            done();
		          })
		          .catch(_ => {});
		          return;
				}
				var total_kg = 0;
				$.each( this.items, function( key, value ) {
				  	total_kg += parseFloat(value.total_kg);
				});

				var total_amount = total_kg * parseFloat(this.model.invoice_price);

				
				var total_with_devided_1_12 = total_amount / 1.12;

				var total_invoice_vat = total_with_devided_1_12 * 0.12;
				var total_invoice_wh_tax = total_with_devided_1_12 * 0.01;

				var total_invoice_price_w_vat = total_amount - total_invoice_vat;
				var total_invoice_amount_due_w_wh_tax = total_invoice_price_w_vat - total_invoice_wh_tax;
				var total_invoice_amount = total_invoice_amount_due_w_wh_tax + total_invoice_vat;
				this.model.total_kg = total_kg;
				this.model.total_invoice_price_w_vat = total_invoice_price_w_vat;
				this.model.total_invoice_price_e_vat = total_amount;
				this.model.total_invoice_vat = total_invoice_vat;
				this.model.total_invoice_wh_tax = total_invoice_wh_tax;

				this.model.total_invoice_amount_due_w_wh_tax = total_invoice_amount_due_w_wh_tax;
				this.model.total_invoice_amount = total_invoice_amount;

				this.model.total_with_devided_1_12 = total_with_devided_1_12;
			},
			addToExpense(){
				let exp = {
					name:'1% withholding tax',
					total_amount: this.model.total_invoice_wh_tax
				}
				this.expenses.push(exp)
			},

			roundToTwo(num) {
				    return +(Math.round(num + "e+2")  + "e-2");
			},
			deleteField(index) {
				let me = this;
				me.items.splice(index,1);
				me.total_sale_price=0;
				me.total_total_kg=0;
				me.total_total_amount=0;
				me.total_total_expences=0;
				me.total_total_net_amount=0;
				$.each( me.items , function( key, value ) {
				  	me.total_sale_price+= value.sale_price;
					me.total_total_kg+= value.total_kg;
					me.total_total_amount+= value.total_amount;
					me.total_total_expences+= value.total_expences;
					me.total_total_net_amount+= value.total_net_amount;
				});
				me.calculateSales();
			},
			deleteFieldExpense(index){
				this.expenses.splice(index,1);
			},

		},
		computed: {
	      
	    },
		props: {
			admin: {
				type: String,
				required: true,
			},
			token: {
				type: String,
				required: true,
			}
		},
		mounted(){
			
			let me = this;
			Promise.all([saleService.getSalesPerStatus('Unshared')]).then((data) => {
				me.items = data[0].data.data;
				me.calculateSales();

				$.each( me.items , function( key, value ) {
				  	me.total_sale_price+= value.sale_price;
					me.total_total_kg+= value.total_kg;
					me.total_total_amount+= value.total_amount;
					me.total_total_expences+= value.total_expences;
					me.total_total_net_amount+= value.total_net_amount;
				});
			}).catch((data)=> {
				console.log(data);
			});
			
			
			
		}
	}

</script>