<template>
	<div class="row">
		<div class="col-md-12">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Create Invoices
						</h3>
					</div>
					
				</div>

			
				<div class="kt-portlet__body">
					
						<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
							<div class="row align-items-center">
								<div class="col-md-12">
									<div class="form-group">
										<label>Invoice Date</label>
										<el-date-picker 
									      v-model="model.invoice_date"
									      type="date"
									      placeholder="Pick a date start"
									     >
									    </el-date-picker> <br/>
										
									</div>
								</div>
								<div class="col-md-6">
									<div class="card" >
									 
									  <div class="card-body">
									  	<div class="row">
										<div class="col-md-10" >
											<div class="form-group">
												<label>Select Adviser/Client</label>
												
												<el-select
												    v-model="filter.client"
												    
												    filterable
												    remote
												    reserve-keyword
												    placeholder="Please enter a keyword for clients"
												    :remote-method="searchClients"
												    :loading="loadingclient">
												    <el-option
												      v-for="(client,index) in clients"
												      :key="client.value"
												      :label="client.label | capitalize"
												      :value="index">
												    </el-option>
												  </el-select>
											</div>
							
										</div>
									<div class="col-md-2" >
											 <button class="btn btn-primary"  @click="dialogVisible = true"><i class="flaticon2-plus"></i> Crete New Client</button>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label>Name</label>
											<input type="text" class="form-control" aria-describedby="emailHelp" v-model="model.client_name" disabled>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Email</label>
											<input type="text" class="form-control" aria-describedby="emailHelp" v-model="model.client_email" disabled>
										</div>
									</div>
									</div>
									  </div>
									</div>
									
								</div>
									
								<div class="col-md-6">
									<div class="card" >
									 
									  <div class="card-body">
										<div class="row">
										

										<div class="col-md-12">
											<div class="form-group">
												<table class="table table-sm table-head-bg-brand">
													<tr>
															<td>Name</td>
															<td>{{logo.name}}</td>
													</tr>
														<tr>
															<td>ABN</td>
															<td>{{logo.abn}}</td>
													</tr>
													<tr>
															<td>Email</td>
															<td>{{logo.email}}</td>
													</tr>
													<tr>
															<td>Address</td>
															<td>{{logo.address}}</td>
													</tr>
													<tr>
															<td>Authorised Rep No.</td>
															<td>{{logo.rep_no}}</td>
													</tr>
													<tr>
															<td>CARN</td>
															<td>{{logo.carn}}</td>
													</tr>
													<tr>
															<td>Phone</td>
															<td>{{logo.phone}}</td>
													</tr>
													<tr>
															<td>Mobile</td>
															<td>{{logo.mobile}}</td>
													</tr>
													<tr>
															<td>Website</td>
															<td>{{logo.website}}</td>
													</tr>
												</table>
											</div>
										</div>
										
									</div>

									</div>
									</div>
								</div>
							  	
								<div class="col-md-12 " style="margin-top:20px;margin-bottom:20px">
									<div class="card" >
									 
									  <div class="card-body">
											<table class="table table-sm table-head-bg-brand"
											v-loading="loading">
												<thead class="thead-inverse">
													<tr>
														
														<th width="400">Description</th>
														
														<th width="400">Amount</th>
														<th width="100">GST</th>
														<th width="100">Total</th>
														<th width="200" style="text-align:right">Action</th>
													</tr>
												</thead>
												<tbody>
														<tr>
															<td>
																<div class="form-group">
																	
																	<textarea class="form-control" aria-describedby="emailHelp" v-model="item.name" placeholder="Description"></textarea>
																	
																</div>
															</td>
															

															<td>
																<div class="form-group">
																	
																	<input type="number" class="form-control" aria-describedby="emailHelp" v-model="item.price"  placeholder="Fee">
																</div>
															</td>
															<td>
																<div class="form-group">
																
																	<input type="number" class="form-control" aria-describedby="emailHelp" v-model="item.gst"  disabled>
																</div>
															</td>
															<td>
																<div class="form-group">
																
																	<input type="number" class="form-control" aria-describedby="emailHelp" v-model="item.total"  disabled>
																</div>
															</td>
															<td style="text-align:right">
																<button class="btn btn-primary" @click="addItem"><i class="flaticon2-plus"></i></button>
															</td>
														</tr>
													<tr v-for="(i, index)  in items">
															
															<td><p><b>{{i.name}}</b></p>
																
															</td>
															
															<td>{{i.price | money}}</td>
															<td>{{i.gst | money}}</td>
															<td style="text-align:right">{{i.total | money}}</td>
															<td style="text-align:right"> <button class="btn btn-danger" @click="deleteField(index)"  ><i class="flaticon-delete"></i></button> </td>
														</tr>
														<tr>	
															<td colspan="3" style="text-align:right"><b>GST:</b></td>
															<td style="text-align:right">
																<b>{{model.gst | money}}</b>
															</td>
															<td ></td>
														</tr>	
														<tr>	
															<td colspan="3" style="text-align:right"><b>Subtotal:</b></td>
															<td style="text-align:right">
																<b>{{model.sub_total | money}}</b>
															</td>
															<td ></td>
														</tr>	
														<!-- <tr>	
															<td colspan="3" style="text-align:right"><b>Adjustments:</b></td>
															<td><input style="text-align:right" type="number" class="form-control" aria-describedby="emailHelp" v-model="model.discount"  placeholder="Discount"></td>
															<td ></td>
														</tr> -->
														<tr>	
															<td colspan="3" style="text-align:right"><b>total:</b></td>
															<td style="text-align:right">
																<b>{{model.total | money}}</b>
															</td>
															<td ></td>
														</tr>	
												</tbody>
											</table>

											</div>
									</div>
										</div>
									<div class="col-md-8">
										<div class="form-group">
											<label>Notes</label>
											<textarea class="form-control" aria-describedby="emailHelp" v-model="model.notes" ></textarea>
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
				<el-dialog
		  title="Create  Client"
		  :visible.sync="dialogVisible"
		  width="30%"
		  :before-close="handleClose">
		  		<form class="kt-form">
					<div class="kt-portlet__body">
						<div class="form-group form-group-last">
							<div class="alert alert-secondary" role="alert">
								<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
									<div class="alert-text">
										Create  Client
									</div>
							</div>
						</div>
						
						<div class="form-group">
							<label>Name</label>
							<input type="text" class="form-control" aria-describedby="emailHelp" v-model="client.name">
							<span class="form-text text-muted">Name of client</span>
						</div>
						
						<div class="form-group">
							<label>Address</label>
							<input type="text" class="form-control" aria-describedby="emailHelp" v-model="client.address">
							<span class="form-text text-muted">Address of client</span>
						</div>

						<div class="form-group">
							<label>Email</label>
							<input type="text" class="form-control" aria-describedby="emailHelp" v-model="client.email">
								<span class="form-text text-muted">Email of client</span>
						</div>	
						
					</div>
					
				</form>
				  <span slot="footer" class="dialog-footer">
				    <el-button @click="dialogVisible = false">Cancel</el-button>
				    <el-button type="primary" @click="saveClient">Confirm</el-button>
				  </span>
		</el-dialog>

		<el-dialog
		  title="Create Logo Details"
		  :visible.sync="dialogVisibleLogo"
		  width="30%"
		  :before-close="handleClose">
		  		<form class="kt-form">
					<div class="kt-portlet__body">
						<div class="form-group form-group-last">
							<div class="alert alert-secondary" role="alert">
								<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
									<div class="alert-text">
										Create Logo
									</div>
							</div>
						</div>
						
						<div class="form-group">
							<label>Name</label>
							<input type="text" class="form-control" aria-describedby="emailHelp" v-model="logo.name">
							<span class="form-text text-muted">Name of logo</span>
						</div>
						
						<div class="form-group">
							<label>Address</label>
							<input type="text" class="form-control" aria-describedby="emailHelp" v-model="logo.address">
							<span class="form-text text-muted">Address of logo</span>
						</div>

						<div class="form-group">
							<label>File</label>
							<file-uploader :token="token" id="fileUploader" ></file-uploader>
							<span class="form-text text-muted">Header found in jpg,png</span>
						
						</div>	
						
					</div>
					
				</form>
				  <span slot="footer" class="dialog-footer">
				    <el-button @click="dialogVisibleLogo = false">Cancel</el-button>
				    <el-button type="primary" @click="saveLogo">Confirm</el-button>
				  </span>
		</el-dialog>
		</div>
		
	
</template>
<style>
.el-select{
	width:100%;
}
</style>
<script type="text/javascript">
	import {mapGetters} from 'vuex';
	import feeService from './../../services/fee/index.js';
	import invoiceService from './../../services/invoice/index.js';
	import clientService from './../../services/client/index.js';
	import logoService from './../../services/logo/index.js';
	import AdviserService from './../../services/adviser/index.js';
	import { debounce } from "debounce";
	export default {
		data(){
			return {
				models: [],
				advisers:[],
				items:[],
				dialogVisible:false,
				dialogVisibleLogo:false,
				filter:{
					adviser_name:'',
					client:'',
				},
				model:{
					sub_total:0.00,
					total:0.00,
					discount:0,
					items:[],
					client_id:0,
					gst:0,
					logo_id:0,
				},
				loading:false,
				loadingclient:false,
				loadinglogo:false,
				item:{
					quantity:1,
					price:0,
					total:0,
					name:'',
					gst:0,
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
			'item.quantity':function(newVal,old) {
				if(newVal != '') {
					this.item.gst  = parseFloat((this.item.price * newVal) * 0.10);
					this.item.total = parseFloat((this.item.price * newVal) + this.item.gst );

				}
			},

			'item.price':function(newVal,old) {
				if(newVal != '') {
					this.item.gst  = parseFloat((this.item.quantity * newVal) * 0.10);
					this.item.total = parseFloat((this.item.quantity * newVal) + this.item.gst ); 
				}
			},
			'items':function(newVal,old){
				let me = this;
				me.model.sub_total = 0;
				me.model.gst=0;
				me.model.total=0;
				$.each( newVal, function( key, value ) {
				  	me.model.sub_total += value.total; 
				  	me.model.gst += value.gst; 
				});

				me.model.total = me.model.sub_total -  me.model.discount; 
			},
			'model.discount':function(newVal,old){
				let me = this;
				me.model.total = me.model.sub_total -  newVal; 
			},
			'filter.client':function(newVal,old){
				
				this.selected_client = this.clients[newVal];
				this.model.client_name = this.selected_client.label;
				this.model.client_email =  this.selected_client.email;

			},
			'filter.logo':function(newVal,old){
				console.log(newVal);
				this.selected_logo = this.logos[newVal];
				this.model.logo_name = this.selected_logo.label;
				this.model.logo_address =  this.selected_logo.address;
				this.model.logo_id =  this.selected_logo.value

			},
			imgSrc(url) {
	    		
	    		this.logo.file = url;
	    	}
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
				
				
				if(me.selected_client.email  == '') {
					me.$message.error('Oops, We cant save this, client is empty.');
					return;
				}
				if(me.model.invoice_date  == '') {
					me.$message.error('Oops, We cant save this, Invoice date  is empty.');
					return;
				}
				if(me.items.length == 0) {
					me.$message.error('Oops, We cant save this, items is empty.');
					return;
				}
				me.model.invoice_date = moment(me.model.invoice_date).format('YYYY-MM-DD');
				me.model.items =me.items;

				if(me.selected_client.type =='C'){
					me.model.client_id = me.selected_client.value;
				}else{
					me.model.client_id = 0;
				}
				invoiceService.update(me.hash_id,me.model).then(function(data) {
					location.href = `/invoices/show/${data.data.data.hash_id}`;
				}).catch(function(data){
					me.$message.error(data.data.data.message);
				})
			},

			saveNew(){
				let me = this;
				
				if(me.selected_client.email  == '') {
					me.$message.error('Oops, We cant save this, client is empty.');
					return;
				}
				if(me.model.invoice_date  == '') {
					me.$message.error('Oops, We cant save this, Invoice date  is empty.');
					return;
				}
				if(me.items.length == 0) {
					me.$message.error('Oops, We cant save this, items is empty.');
					return;
				}
				me.model.invoice_date = moment(me.model.invoice_date).format('YYYY-MM-DD');
				me.model.items =me.items;
				if(me.selected_client.type =='C'){
					me.model.client_id = me.selected_client.value;
				}else{
					me.model.client_id = 0;
				}
				
				invoiceService.store(me.model).then(function(data) {
					me.model =  {
						sub_total:0.00,
						total:0.00,
						discount:0,
						items:[],
						client_id:0
					};
				}).catch(function(data) {
					me.$message.error(data.data.data.message);
				})
			}, 
			saveClient() {
				let me = this;
				
				if(me.client.email  == '') {
					me.$message.error('Oops, We cant save this, email is empty.');
					return;
				}

				if(me.client.name  == '') {
					me.$message.error('Oops, We cant save this, name is empty.');
					return;
				}

				
				
				clientService.store(me.client).then(function(data) {
					me.selected_client = data.data.data;
					me.selected_client.type ='C';
					me.selected_client.value =me.selected_client.id;
					me.model.client_name = me.selected_client.name;
					me.model.client_email =  me.selected_client.email;
					me.dialogVisible = false;
					me.$message('Successfully saved client');
				}).catch(function(data){
					me.$message.error(data.data.data.message);
				})
			},
			addItem(){
				if(this.item.name == ''){
					this.$confirm('Item name is empty')
		          .then(_ => {
		            done();
		          })
		          .catch(_ => {});
		          return;
				}
				this.items.push(this.item);
				this.item = {
					quantity:1,
					price:0,
					total:0,
					name:'',
				}
			},

			searchClients(query){
				let me = this;

				if (query !== '') {
		          me.loadingclient = true;
		          me.clients=[];	
					clientService.search({filter:query}).then(function(response){
						var advisers = response.data.data;
						for(let i =0;i< response.data.data.length;i++) {

							let adviser = {
								label: advisers[i].name,
								email: advisers[i].email,
								value: advisers[i].id,
								type: advisers[i].type_data,
							}

							me.clients.push(adviser);
						}
						me.loadingclient = false;
					});
		        } else {
		          me.clients = [];
		        } 
			},

			searchLogos(query){
				let me = this;

				if (query !== '') {
		          me.loadinglogo = true;
		          me.logos=[];	
					logoService.search({filter:query}).then(function(response){
						var advisers = response.data.data;
						for(let i =0;i< response.data.data.length;i++) {

							let adviser = {
								label: advisers[i].name,
								address: advisers[i].address,
								value: advisers[i].id,
							
							}

							me.logos.push(adviser);
						}
						me.loadinglogo = false;
					});
		        } else {
		          me.logos = [];
		        } 
			},

			saveLogo() {
				let me = this;
				
				if(me.logo.address  == '') {
					me.$message.error('Oops, We cant save this, address is empty.');
					return;
				}

				if(me.logo.name  == '') {
					me.$message.error('Oops, We cant save this, name is empty.');
					return;
				}
				if(me.logo.file  == '') {
					me.$message.error('Oops, We cant save this, file is empty.');
					return;
				}

				
				
				logoService.store(me.logo).then(function(data) {
					me.selected_logo = data.data.data;
					
					me.model.logo_id = me.selected_logo.id;
					me.model.logo_name = me.selected_logo.name;
					me.model.logo_address =  me.selected_logo.address;
					me.dialogVisibleLogo = false;
					me.$message('Successfully saved logo');
				}).catch(function(data){
					me.$message.error(data.data.data.message);
				})
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
			token: {
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
			Promise.all([invoiceService.info(this.hash_id)]).then((data) => {
				
				me.model = data[0].data.data;
				me.selected_client = me.model.client;
				me.selected_client.type ='C';
				me.selected_client.value=me.selected_client.id;
				me.model.client_name = me.selected_client.name;
				me.model.client_email =  me.selected_client.email;
				me.items = me.model.items;
				me.selected_logo = me.model.logo;

				if(me.selected_logo != null){
					me.logo = me.model.logo;
					me.model.logo_id = me.selected_logo.id;
					me.model.logo_name = me.selected_logo.name;
					me.model.logo_address =  me.selected_logo.address
				}
				
			}).catch((data)=> {
				console.log(data);
			});
		}
	}

</script>