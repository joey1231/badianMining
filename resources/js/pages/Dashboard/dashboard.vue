  <template>

	<div >
	<div class="row">
		<div class="col-lg-4" style="background:#fff;">
			<!--begin:: Widgets/Inbound Bandwidth-->
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label">
							<h3 class="kt-portlet__head-title">
															Number of Sales
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
															Last Month Sales
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{lastMonthSales | NumberFormat }}</span>
								<span class="kt-widget20__desc">Number of sales</span>
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
															Current Month Sales
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
															Total Net Amount of Sales
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{salesAmount | money}}</span>
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
														Total Net Amount Of Last Month Sales
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{lastMonthSalesAmount  | money }}</span>
								<span class="kt-widget20__desc">Sales</span>
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
														Total Net Amount Of Current Month Sales
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{currentMonthSalesAmount  | money }}</span>
								<span class="kt-widget20__desc">Sales</span>
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
															Yearly Sales
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{yearlySales | NumberFormat }}</span>
								<span class="kt-widget20__desc">Number of Sales</span>
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
															Total Amount of Yearly Sales
							</h3>
						</div>
					</div>
					<div class="kt-portlet__body kt-portlet__body--fluid">
						<div class="kt-widget20">
							<div class="kt-widget20__content kt-portlet__space-x">
								<span class="kt-widget20__number kt-font-brand">{{yearlySalesAmount | money }}</span>
								<span class="kt-widget20__desc">Total Amount of sales</span>
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
															Monthly Sales
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
															Monthly Sales Amount
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
			}
		},

		methods: {
			
		},
		mounted(){
			Promise.all([
				dashService.info(),
				
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
					
			});
		}
	}
</script>