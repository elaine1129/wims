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
        <!-- <TableComponent
          name="classB"
          :data="data.cycle_count_schedules.schedules_classB"
        ></TableComponent> -->
      </TabPane>
      <TabPane label="Completed" name="completed">
        <!-- <TableComponent
          name="classC"
          :data="data.cycle_count_schedules.schedules_classC"
        ></TableComponent> -->
      </TabPane>
    </Tabs>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import TableComponent from "../manager/table.vue";

export default {
  components: {
    PageComponent,
    TableComponent,
  },
  data() {
    return {
      data: {
        cycle_count_schedules: [],
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
    $(document).ready(function () {
      $("#upcoming").DataTable({
        order: [[4, "asc"]],
      });
    });
  },
};
</script>