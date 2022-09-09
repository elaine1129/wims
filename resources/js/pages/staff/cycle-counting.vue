<template>
  <PageComponent title="Cycle Counting">
    <Tabs value="name1" type="card">
      <TabPane label="Upcoming" name="upcoming">
        <CycleCountUpcomingTableComponent
          name="upcoming"
          :data="data.cycle_count_schedules"
          @newCycleCount="cycleCountInChild"
        ></CycleCountUpcomingTableComponent>
      </TabPane>
      <TabPane label="Pending Approval" name="pending">
        <CycleCountReportTableComponent
          name="pending"
          :data="data.pending_cycle_counts"
        ></CycleCountReportTableComponent>
      </TabPane>
      <TabPane label="Completed" name="completed">
        <CycleCountReportTableComponent
          name="completed"
          :data="data.completed_cycle_counts"
        ></CycleCountReportTableComponent>
      </TabPane>
    </Tabs>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import CycleCountUpcomingTableComponent from "./components/cycle-count-upcoming-table.vue";
import CycleCountReportTableComponent from "./components/cycle-count-report-table.vue";
// import axiosClient from "../../axios";
export default {
  components: {
    PageComponent,
    CycleCountUpcomingTableComponent,
    CycleCountReportTableComponent,
  },
  watch: {
    "data.cycle_count_schedules": {
      handler() {
        $("#upcoming").DataTable({
          order: [[4, "asc"]],
        });
      },
      deep: true,
      flush: "post",
    },
    "data.pending_cycle_counts": {
      handler() {
        $("#pending").DataTable();
      },
      deep: true,
      flush: "post",
    },
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
    await this.fetchData();

    // $(document).ready(function () {
    //   $("#upcoming").DataTable({
    //     order: [[4, "asc"]],
    //   });
    // });
    // $(document).ready(function () {
    //   $("#pending").DataTable();
    // });
    $(document).ready(function () {
      $("#completed").DataTable();
    });
  },
  methods: {
    async fetchData() {
      await this.$axiosClient;
      const res = await this.$axiosClient.get("/schedules");
      console.log(res);
      this.data.cycle_count_schedules = res.data.data;
      $("#upcoming").DataTable().destroy();
      const reportRes = await this.$axiosClient.get("/cycle-counts");

      console.log(reportRes);
      if (reportRes.status == 200) {
        this.data.pending_cycle_counts = _.filter(reportRes.data.data, {
          status: "PENDING",
        });
        $("#pending").DataTable().destroy();
        this.data.completed_cycle_counts = _.filter(reportRes.data.data, {
          status: "COMPLETED",
        });
        console.log("pending", this.data.pending_cycle_counts);
      }
    },
    cycleCountInChild() {
      this.fetchData();
    },
  },
};
</script>