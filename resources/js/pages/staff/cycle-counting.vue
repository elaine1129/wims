<template>
  <PageComponent title="Cycle Counting">
    <Tabs value="name1" type="card">
      <TabPane label="Upcoming" name="upcoming">
        <TableComponent
          name="upcoming"
          :data="data.cycle_count_schedules"
        ></TableComponent>
      </TabPane>
      <TabPane label="Pending Approval" name="pending">
        <TableReportComponent
          name="pending"
          :data="data.pending_cycle_counts"
        ></TableReportComponent>
      </TabPane>
      <TabPane label="Completed" name="completed">
        <TableReportComponent
          name="completed"
          :data="data.completed_cycle_counts"
        ></TableReportComponent>
      </TabPane>
    </Tabs>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import TableComponent from "./components/cycle-count-table.vue";
import TableReportComponent from "./components/cycle-count-report-table.vue";
import axiosClient from "../../axios";
export default {
  components: {
    PageComponent,
    TableComponent,
    TableReportComponent,
  },
  data() {
    return {
      data: {
        cycle_count_schedules: [],
        pending_cycle_counts: [],
        completed_cycle_counts: [],
      },
    };
  },
  async created() {
    const res = await this.callApi(
      "GET",
      "/api/getSchedulesByStaff/" + this.$store.getters.getUser.id
    );
    console.log(res);
    this.data.cycle_count_schedules = res.data.data;
    // const reportRes = await this.callApi("GET", "/api/cycle-counts");
    const reportRes = await axiosClient.get("/cycle-counts");

    console.log(reportRes);
    if (reportRes.status == 200) {
      this.data.pending_cycle_counts = _.filter(reportRes.data.data, {
        status: "PENDING",
      });
      this.data.completed_cycle_counts = _.filter(reportRes.data.data, {
        status: "COMPLETED",
      });
      console.log("pending", this.data.pending_cycle_counts);
    }

    $(document).ready(function () {
      $("#upcoming").DataTable({
        order: [[4, "asc"]],
      });
    });
    $(document).ready(function () {
      $("#pending").DataTable();
    });
    $(document).ready(function () {
      $("#completed").DataTable();
    });
  },
};
</script>