<template>
	<div class="row">
		<div class="col-md-12">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							List of Sales
						</h3>
					</div>
					<div class="kt-portlet__head-toolbar">
						<div class="kt-portlet__head-wrapper">
							<button type="button" class="btn btn-brand btn-icon-sm"  aria-haspopup="true" aria-expanded="false" @click="composeNew">
								<i class="flaticon2-plus"></i> Add New
							</button>
											
											
						</div>
					</div>
				</div>

				<div class="kt-portlet__body">
						<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
							<div class="row align-items-center">
											<div class="col-xl-8 order-2 order-xl-1">
												<div class="row align-items-center">
													<div class="col-md-6 kt-margin-b-20-tablet-and-mobile">
														<div class="kt-input-icon kt-input-icon--left">
															<input type="text" class="form-control" placeholder="Search..." id="generalSearch"
															v-model="search" @keydown="searchData">
															<span class="kt-input-icon__icon kt-input-icon__icon--left">
																<span><i class="la la-search"></i></span>
															</span>
														</div>
													</div>
													
												</div>
											</div>
									
										</div>
						</div>
				</div>
				<div class="kt-portlet__body	kt-portlet__body--fit">
					<div class="kt-form kt-form--label-right kt-margin-t-20  kt-margin-l-20 kt-margin-r-20 kt-margin-b-10">
						<div class="row align-items-center">
								<div class="col-xl-12 order-2 order-xl-1">
										<el-tabs v-model="display" type="card">
										<el-tab-pane label="Sales" name="invoices">
											<div class="row">
												<div class="col-md-12">
													<table class="table table-sm table-head-bg-brand"
														v-loading="loading">
															<thead class="thead-inverse">
																<tr>
																	
																	<th width="200">Name</th>
																	
																	<th width="200">Status</th>
																	<th width="130">Total KG/Tons</th>

																		
																	<th width="130">Total  Amount</th>
																	
																	<th width="130">Total Expenses</th>
																	<th width="100">Net Total Amount</th>
																	
																	
																	<th  width="200">Action</th>
																</tr>
															</thead>
															<tbody>
																		<tr v-for="model in models">
																		
																		<td>{{model.name}}</td>
																		<td>{{model.status | capitalize}}</td>
																		<td>{{model.total_kg| NumberFormat}}</td>
																		
																		<td>{{model.total_amount | NumberFormat}}</td>
																		<td>{{model.total_expences | NumberFormat}}</td>
																		<td>{{model.total_net_amount | NumberFormat}}</td>
																		
																		
																		
																		<td>   <button class="btn btn-primary" @click="showmodel(model.hash_id)"><i class="flaticon-visible"></i></button>
																			 <button class="btn btn-success" @click="sharePaid(model.hash_id)" v-if="model.status == 'pending'"><i class="fa fa-check"></i></button>
																		</td>
																	</tr>				
															</tbody>
														</table>
												</div>

												<div class="col-md-12 "  v-show="models.length > 0">
													<el-pagination
															  :page-size="20"
															  :pager-count="11"
															  layout="prev, pager, next"
															  :total="pagenation.total"
															  :current-page.sync="pagenation.currentPage"
															  @current-change="currentChange"
															  :hide-on-single-page="showPage"
															  >
															</el-pagination>
												</div>
											</div>
											
											
										</el-tab-pane>
											
									</el-tabs>
								</div>
						</div>
					</div>
				</div>
								<!--end::Section-->
			</div>
			<div class="kt-portlet__foot">
						
			</div>
			</div>
		</div>
		
	</div>
</template>
<script type="text/javascript">
	
	import shareService from './../../services/share/index.js';
	import { debounce } from "debounce";
	export default {
		data(){
			return {
				models: [],
				pagenation:{
					total:0,
					currentPage:0,
				},

				
				showPage:false,
			
				search:'',
				loading: false,
				
				tags:[],
				tag_id:0,
				display:'invoices',
			

			}
		},
		methods: {
			currentChange(page) {
				this.dataFetch(page)
			},
			dataFetch(page) {
				var me = this;
				me.loading = true;
				shareService.search({page:page,search:me.search, status: true}).then(function(response){  
					 me.models = response.data.data.data;
					 me.pagenation.total = response.data.data.total;
					 me.pagenation.currentPage = response.data.data.current_page;
					 me.showPage = response.data.total > 5;
					 me.loading = false;
		        }).catch(function(response){
		           console.log(response)
		            me.loading = false;
		        });
			},

			
			composeNew() {
				location.href=`/shares/create`;
			},

			showTag(model){
				if(model.tag != null) {
					return model.tag.name;
				}
				return '';
			},
			updateTag(model){
				let me = this;
				periodService.update(model.hash_id,model).then(function(data) {
					me.$message.success(data.data.message);
				}).catch(function(data){
					me.$message.error(data.data.data.message);
				})
			},
			searchData: debounce(function() {
				var me = this;
				me.models = [];
		
				me.dataFetch(me.pagenation.currentPage);
				
			},400),

			sharePaid(hash_id) {
				let me = this;
				 me.$confirm('This will permanently change status to Paid. Continue?', 'Warning', {
			          confirmButtonText: 'OK',
			          cancelButtonText: 'Cancel',
			          type: 'warning'
			    }).then(() => {
			        shareService.sharePaid(hash_id).then(function(response){  
						me.dataFetch(me.pagenation.currentPage);
				    }).catch(function(response){
				           console.log(response)
				    });
			    }).catch(() => {
			                    
			    });
			},

			
			editShema(hash_id) {
				location.href=`/invoices/edit/${hash_id}`;
			},
			showmodel(hash_id) {
				location.href=`/shares/show/${hash_id}`;
			},
			sendToQueue(id) {

				let me = this;
				 me.$confirm('Are you sure to send it to queue again?', 'Warning', {
			          confirmButtonText: 'OK',
			          cancelButtonText: 'Cancel',
			          type: 'warning'
			    }).then(() => {
			        invoiceService.sendToQueue(id).then(function(response){  
						me.dataFetch(me.pagenation.currentPage);
				    }).catch(function(response){
				           console.log(response)
				    });
			    }).catch(() => {
			                    
			    });
			},
			download(id) {
				///location.href=`/invoices/download/${id}`;
				window.open(`/invoices/download/${id}`, '_blank');
			},
		},
		mounted(){
			let me = this;
			
			this.dataFetch(1);
			

		}
	}

</script>