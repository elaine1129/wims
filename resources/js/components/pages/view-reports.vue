<template>
  <PageComponent title="View Report">
    <Tabs type="card">
      <TabPane label="Daily Report">
        <table id="daily-report" class="display" style="width: 100%">
          <thead>
            <tr>
              <th>Warehouse ID</th>
              <th>Warehouse Name</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="report in data.daily_reports_unique" :key="report.id">
              <td>{{ report.warehouse.id }}</td>
              <td>{{ report.warehouse.name }}</td>
              <td>{{ convertDate(report.created_at) }}</td>
              <td>
                <Button type="primary" @click="viewDailyReport(report)"
                  >View</Button
                >
              </td>
            </tr>
          </tbody>
        </table>
        <Modal
          v-model="dailyReportModal"
          class-name="vertical-center-modal"
          width="800"
          :closable="false"
          :mask-closable="false"
        >
          <template #header>
            <p>Daily Report</p>
          </template>
          <div class="grid grid-cols-3 gap-3">
            <div>Warehouse ID</div>
            <div>
              {{
                data.selectedDailyReport[0]
                  ? data.selectedDailyReport[0].warehouse.id
                  : "-"
              }}
            </div>
          </div>
          <div class="grid grid-cols-3 gap-3">
            <div>Warehouse Name</div>
            <div>
              {{
                data.selectedDailyReport[0]
                  ? data.selectedDailyReport[0].warehouse.name
                  : "-"
              }}
            </div>
          </div>
          <div class="grid grid-cols-3 gap-3">
            <div>Date of Report</div>
            <div>
              {{
                data.selectedDailyReport[0]
                  ? convertDate(data.selectedDailyReport[0].created_at)
                  : "-"
              }}
            </div>
          </div>
          <div>
            <StockTableComponent
              :name="stockTableID"
              :data="data.selectedDailyReport"
            ></StockTableComponent>
          </div>

          <template #footer class="d-flex justify-content-center">
            <Button type="primary" @click="closeDailyReportModal">OK</Button>
          </template>
        </Modal>
      </TabPane>
      <TabPane
        v-if="this.$store.getters.getUser.role == 'Manager'"
        label="Cycle Counting Approval Report"
      >
        <table id="approval-report" class="display" style="width: 100%">
          <thead>
            <tr>
              <th>Cycle Count ID</th>
              <th>SKU ID</th>
              <th>Inventory Name</th>
              <th>Warehouse Name</th>
              <th>Staff Name</th>
              <th>Recorded Count</th>
              <th>Actual Count</th>
              <th>Date Counted</th>
              <th>Variance</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="cycle_counting in data.cycle_countings_pending"
              :key="cycle_counting.id"
            >
              <td>{{ cycle_counting.id }}</td>
              <td>{{ cycle_counting.schedule.sku.id }}</td>
              <td>{{ cycle_counting.schedule.sku.inventory.name }}</td>
              <td>
                {{ cycle_counting.schedule.sku.inventory.warehouse.name }}
              </td>
              <td>{{ cycle_counting.schedule.staff.name }}</td>
              <td>{{ cycle_counting.recorded_count }}</td>
              <td>{{ cycle_counting.actual_count }}</td>
              <td>{{ convertDate(cycle_counting.created_at) }}</td>
              <td>{{ cycle_counting.variance }}</td>
              <td>
                <div class="flex items-center gap-x-3">
                  <Button
                    type="primary"
                    class="basis-1/2"
                    @click="showApproveCycleCountModal(cycle_counting)"
                    >Approve</Button
                  >
                  <Button
                    type="primary"
                    class="basis-1/2"
                    @click="showRejectCycleCountModal(cycle_counting)"
                    >Reject</Button
                  >
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <Modal
          v-model="approveCycleCountModal"
          title="Approve cycle count report"
          @on-ok="confirmApprove"
          @on-cancel="approveCycleCountModal = false"
          okText="Confirm"
          cancalText="Cancel"
        >
          <Alert type="warning" show-icon>
            Are you sure to approve the cycle count report?
            <template #desc>
              This approval will modify the system count of Inventory ID({{
                selectedCycleCount
                  ? selectedCycleCount.schedule.sku.inventory.id
                  : "-"
              }}) with the variances:
              <span class="font-bold">{{
                selectedCycleCount ? selectedCycleCount.variance : "-"
              }}</span>
            </template>
          </Alert>
        </Modal>
        <Modal
          v-model="rejectCycleCountModal"
          title="Reject cycle count report"
          @on-ok="confirmReject"
          @on-cancel="rejectCycleCountModal = false"
          okText="Confirm"
          cancalText="Cancel"
        >
          <Alert type="warning" show-icon>
            Are you sure to reject the cycle count report?
            <template #desc>
              This reject will not be adjusting the inventory count to the
              actual counted value.
              <Checkbox v-model="recountSKU"
                >Reassign staff responsible ({{
                  selectedCycleCount
                    ? selectedCycleCount.schedule.staff.name
                    : "-"
                }}) to recount the SKU.</Checkbox
              >
            </template>
          </Alert>
        </Modal>
      </TabPane>
      <TabPane label="Cycle Count Summary Report">
        <SummaryReportTableComponent
          name="summary-report"
          :data="data.cycle_countings_completed"
        ></SummaryReportTableComponent>
      </TabPane>
    </Tabs>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import StockTableComponent from "../tables/stock-table.vue";
import SummaryReportTableComponent from "../tables/summary-report-table.vue";
import moment from "moment";
export default {
  components: {
    PageComponent,
    StockTableComponent,
    SummaryReportTableComponent,
  },
  data() {
    return {
      data: {
        daily_reports_unique: [],
        daily_reports_grouped: [],
        selectedDailyReport: [],
        cycle_countings_pending: [],
        cycle_countings_completed: [],
      },
      dailyReportModal: false,
      stockTableID: "",
      approveCycleCountModal: false,
      selectedCycleCount: null,
      rejectCycleCountModal: false,
      recountSKU: false,
    };
  },
  async created() {
    var res;
    await this.$axiosClient.get("/stocks").then((response) => {
      if (this.$store.getters.getUser.role == "Admin") {
        console.log(response);
        res = _.cloneDeep(response.data);

        for (const item in res) {
          res[item] = this.groupDataByDate(res[item]);
        }
        console.log("res grouprd", res);
        this.data.daily_reports_grouped = res;
        var temp = _.cloneDeep(response.data);
        for (const item in temp) {
          this.data.daily_reports_unique.push(
            _.uniqBy(temp[item], (stock) => {
              //get one data from each date group to display
              return this.convertDate(stock.created_at);
            })
          );
        }
        this.data.daily_reports_unique = _.flatten(
          this.data.daily_reports_unique
        );
        console.log("unique", this.data.daily_reports_unique);
      } else {
        res = response.data.data;
        const res_grouped = this.groupDataByDate(res); //group the data by date
        console.log("res grouprd", res_grouped);

        this.data.daily_reports_grouped = res_grouped;
        this.data.daily_reports_unique = _.uniqBy(res, (stock) => {
          //get one data from each date group to display
          return this.convertDate(stock.created_at);
        });
        console.log("unique", this.data.daily_reports_unique);
      }
    });

    const cycleCountingsRes = await this.$axiosClient.get("/cycle-counts");
    console.log(cycleCountingsRes);
    this.data.cycle_countings_pending = _.filter(cycleCountingsRes.data.data, {
      status: "PENDING",
    });
    console.log(this.data.cycle_countings_pending);
    this.data.cycle_countings_completed = _.filter(
      cycleCountingsRes.data.data,
      {
        status: "COMPLETED",
      }
    );
    console.log(this.data.cycle_countings_completed);
    $(document).ready(function () {
      $("#daily-report").DataTable();
    });
    $(document).ready(function () {
      $("#approval-report").DataTable();
    });
    $(document).ready(function () {
      $("#summary-report").DataTable();
    });
  },
  methods: {
    viewDailyReport(report) {
      if (this.$store.getters.getUser.role == "Admin") {
        this.data.selectedDailyReport = _.get(
          this.data.daily_reports_grouped[report.warehouse_id],
          this.convertDate(report.created_at)
        );
        console.log(this.data.selectedDailyReport);
      } else {
        console.log(this.data.daily_reports_grouped);
        this.data.selectedDailyReport = _.get(
          this.data.daily_reports_grouped,
          this.convertDate(report.created_at)
        );
        console.log(this.data.selectedDailyReport);
      }

      this.stockTableID =
        "stock_" +
        this.data.selectedDailyReport[0].warehouse.id +
        "_" +
        this.convertDate(this.data.selectedDailyReport[0].created_at);
      this.dailyReportModal = true;
    },
    closeDailyReportModal() {
      var table = $(`#${this.stockTableID}`).DataTable();
      table.destroy();
      this.dailyReportModal = false;
    },
    async confirmApprove() {
      let params = {
        cycle_counting_id: this.selectedCycleCount.id,
        ira: (
          1 -
          Math.abs(this.selectedCycleCount.variance) /
            this.selectedCycleCount.schedule.sku.inventory.qty_on_hand
        ).toFixed(3),
        inventory_id: this.selectedCycleCount.schedule.sku.inventory.id,
        variance: this.selectedCycleCount.variance,
      };
      console.log(params);
      const res = await this.$axiosClient.post("/approve-cycle-count", params);
      // console.log(res);
    },
    showApproveCycleCountModal(cycle_count) {
      this.selectedCycleCount = cycle_count;
      this.approveCycleCountModal = true;
    },

    showRejectCycleCountModal(cycle_count) {
      this.selectedCycleCount = cycle_count;
      console.log(this.selectedCycleCount);
      this.rejectCycleCountModal = true;
    },
    async confirmReject() {
      let params = {
        cycle_counting_id: this.selectedCycleCount.id,
        recount: this.recountSKU,
        schedule_date: this.recountSKU ? moment().format("YYYY-MM-DD") : null,
      };
      console.log(params);
      const res = await this.$axiosClient.post("/reject-cycle-count", params);
      console.log(res);
    },
  },
};
</script>