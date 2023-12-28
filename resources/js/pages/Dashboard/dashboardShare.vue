  <template>

	<div >
	<div class="row">
		<div class="col-md-5">
			 <div class="row">
		 		<div class="col-md-12">
		 				<div class="card" >
											 
											  <div class="card-body">
											  		<h3>Investor Info</h3>
													<table class="table table-sm table-head-bg-brand">
														<thead class="thead-inverse">
																<tr>
																	<th>Name</th>
																	<th>{{user.name | capitalize}}</th>
																</tr>

																<tr>
																	<th>Invest On</th>
																	<th>{{investorDetail.invest_on | DateFormat}}</th>
																</tr>

																<tr>
																	<th>Share Start on</th>
																	<th>{{investorDetail.share_start | DateFormat}}</th>
																</tr>

																<tr>
																	<th>Total Invest</th>
																	<th>{{investorDetail.invest_value | money}}</th>
																</tr>

																<tr>
																	<th>Share Type</th>
																	<th>{{investorDetail.share_type | capitalize}}</th>
																</tr>
																<tr>
																	<th>Share Value</th>
																	<th>{{investorDetail.share_value | money}}</th>
																</tr>

																<tr>
																	<th>Unpaid Shares</th>
																	<th> {{unpaid | money}}</th>
																</tr>
														</thead>
													</table>
													<hr/>
													
													</div>
												</div>
		 		</div>

		 		<div class="col-md-12 " style="margin-top:20px;margin-bottom:20px">
											<div class="card" >
											 
											  <div class="card-body">
											  		<h3>Shares History</h3>
													<table class="table table-sm table-head-bg-brand"
													v-loading="loading">
														<thead class="thead-inverse">
															<tr>
																
																<th width="400">Name</th>
																<th width="400">Status</th>
															
																<th width="400">Total Amount</th>

																
															</tr>
														</thead>
														<tbody>
																
																<tr v-for="(i, index)  in shared">
																	
																	<td><p><b>{{i.user.name}}</b></p>
																	<td><p><b>{{i.status | capitalize}}</b></p>	
																	</td>

																	
																	<td>{{i.amount | NumberFormat}}</td>
																	
																</tr>
																
														</tbody>
													</table>
													<hr/>
													
													</div>
												</div>
											</div>
		 </div>
		</div>
		<div class="col-md-7">
			<div class="row">
		<div class="col-lg-4" style="background:#fff;">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
															Number of Shares
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{salesCount | NumberFormat}}</span>
								<span class="kt-widget20__desc">Number of Sales Recorded</span>
							</div>
														<!-- <div class="kt-widget20__chart" style="height:130px;">
															<canvas id="kt_chart_bandwidth1"></canvas>
														</div> -->
						</div>
					</div>
			</div>
		</div>

		<div class="col-lg-4" style="background:#fff;">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
															Last Month Shares
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{lastMonthSales | NumberFormat }}</span>
								<span class="kt-widget20__desc">Number of shares</span>
							</div>
														<!-- <div class="kt-widget20__chart" style="height:130px;">
															<canvas id="kt_chart_bandwidth1"></canvas>
														</div> -->
						</div>
					</div>
			</div>
		</div>

		<div class="col-lg-4" style="background:#fff;">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
															Current Month Shares
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{currentMonthSales | NumberFormat }}</span>
								<span class="kt-widget20__desc">Number of sales</span>
							</div>
														<!-- <div class="kt-widget20__chart" style="height:130px;">
															<canvas id="kt_chart_bandwidth1"></canvas>
														</div> -->
						</div>
					</div>
			</div>
		</div>
		
	</div>

	<div class="row">
		<div class="col-lg-4" style="background:#fff;">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
															Total Amount of Shares
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{salesAmount | money}}</span>
								<span class="kt-widget20__desc">Amount of Shares  Recorded</span>
							</div>
														<!-- <div class="kt-widget20__chart" style="height:130px;">
															<canvas id="kt_chart_bandwidth1"></canvas>
														</div> -->
						</div>
					</div>
			</div>
		</div>

		<div class="col-lg-4" style="background:#fff;">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
														Total  Amount Of Last Month Shares
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{lastMonthSalesAmount  | money }}</span>
								<span class="kt-widget20__desc">Shares</span>
							</div>
														<!-- <div class="kt-widget20__chart" style="height:130px;">
															<canvas id="kt_chart_bandwidth1"></canvas>
														</div> -->
						</div>
					</div>
			</div>
		</div>

		<div class="col-lg-4" style="background:#fff;">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
														Total  Amount Of Current Month Shares
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{currentMonthSalesAmount  | money }}</span>
								<span class="kt-widget20__desc">Shares</span>
							</div>
														<!-- <div class="kt-widget20__chart" style="height:130px;">
															<canvas id="kt_chart_bandwidth1"></canvas>
														</div> -->
						</div>
					</div>
			</div>
		</div>
		
	</div>

	<div class="row">
		<div class="col-lg-6" style="background:#fff;">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
															Yearly Shares
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{yearlySales | NumberFormat }}</span>
								<span class="kt-widget20__desc">Number of Shares</span>
							</div>
														<!-- <div class="kt-widget20__chart" style="height:130px;">
															<canvas id="kt_chart_bandwidth1"></canvas>
														</div> -->
						</div>
					</div>
			</div>
		</div>

		<div class="col-lg-6 " style="background:#fff;">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
															Total Amount of Yearly Shares
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{yearlySalesAmount | money }}</span>
								<span class="kt-widget20__desc">Total Amount of Shares</span>
							</div>
														<!-- <div class="kt-widget20__chart" style="height:130px;">
															<canvas id="kt_chart_bandwidth1"></canvas>
														</div> -->
						</div>
					</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-xl-6 order-lg-1 order-xl-1" style="background:#fff">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
															Monthly Shares
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<bar-chart :styles="myStyles" v-if="!loading" :chart-data="yearlyDataSet" ></bar-chart>
							</div>
														<!-- <div class="kt-widget20__chart" style="height:130px;">
															<canvas id="kt_chart_bandwidth1"></canvas>
														</div> -->
						</div>
					</div>
			</div>
		</div>
		<div class="col-lg-12 col-xl-6 order-lg-1 order-xl-1" style="background:#fff">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
															Monthly Shares Amount
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<bar-chart :styles="myStyles" v-if="!loading" :chart-data="yearlyDataSetAmount" ></bar-chart>
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
<script>
	
	import dashService from './../../services/dashboard/index.js';
	import barChart from './../../components/barGraph.js';
	export default {
		components: {
	      barChart
	    },
		data(){

			return {
				myStyles: {
			        height: '300px',
			        width: '100%',
			        position: 'relative',
			      },
				salesCount:0,
				currentMonthSales:0.00,
				
				lastMonthSalesAmount:0.00,
				salesAmount:0,
				currentMonthSalesAmount:0.00,
				
				lastMonthSales:0.00,
				yearlySales:0.00,
				yearlySalesAmount:0.00,
				yearlyDataSet: null,
				yearlyDataSetAmount: null,
				loading: true,
				shared:[],
				unpaid:0,
				user:{},
				investorDetail: {},
			}
		},

		methods: {
			
		},
		mounted(){
			Promise.all([
				dashService.infoShare(),
				
				]).then((data) =>{
					this.salesCount = data[0].data.data.salesCount;
					this.currentMonthSales = data[0].data.data.currentMonthSales;
					this.lastMonthSalesAmount = data[0].data.data.lastMonthSalesAmount;
					this.salesAmount = data[0].data.data.salesAmount;
					this.currentMonthSalesAmount = data[0].data.data.currentMonthSalesAmount;
					this.lastMonthSales = data[0].data.data.lastMonthSales;
					this.yearlySales = data[0].data.data.yearlySales;
					this.yearlySalesAmount = data[0].data.data.yearlySalesAmount;
					this.yearlyDataSet = data[0].data.data.yearlyDataSet;
					this.yearlyDataSetAmount = data[0].data.data.yearlyDataSetAmount;
					this.loading = false;
					this.shared = data[0].data.data.shares
					this.unpaid = data[0].data.data.unpaid
					this.user=  data[0].data.data.user
					this.investorDetail=  data[0].data.data.investorDetail
					
			});
		}
	}
</script>