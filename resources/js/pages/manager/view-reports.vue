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
      <TabPane label="cycle-count-approval-report"
        >Cycle Count Approval Reports</TabPane
      >
      <TabPane label="cycle-count-summary-report">Summary Reports</TabPane>
    </Tabs>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import StockTableComponent from "../../components/stock-table.vue";

export default {
  components: {
    PageComponent,
    StockTableComponent,
  },
  data() {
    return {
      data: {
        daily_reports_unique: [],
        daily_reports_grouped: [],
        selectedDailyReport: [],
      },
      dailyReportModal: false,
      stockTableID: "",
    };
  },
  async created() {
    const res = await this.$axiosClient.get("/stocks");
    console.log(res);
    const res_grouped = this.groupDataByDate(res.data.data);
    console.log(res_grouped);

    this.data.daily_reports_grouped = res_grouped;
    this.data.daily_reports_unique = _.uniqBy(res.data.data, (stock) => {
      return this.convertDate(stock.created_at);
    });
    console.log(this.data.daily_reports_unique);

    $(document).ready(function () {
      $("#daily-report").DataTable();
    });
  },
  methods: {
    viewDailyReport(report) {
      this.data.selectedDailyReport = _.get(
        this.data.daily_reports_grouped,
        this.convertDate(report.created_at)
      );
      console.log(this.data.selectedDailyReport);
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
  },
};
</script>