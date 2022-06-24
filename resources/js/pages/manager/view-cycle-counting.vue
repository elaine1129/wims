<template>
  <PageComponent title="View Cycle Counting">
    <Tabs value="name1" type="card">
      <TabPane label="Class A" name="classA">
        <TableComponent
          name="classA"
          :data="data.cycle_count_schedules.schedules_classA"
        ></TableComponent>
      </TabPane>
      <TabPane label="Class B" name="classB">
        <TableComponent
          name="classB"
          :data="data.cycle_count_schedules.schedules_classB"
        ></TableComponent>
      </TabPane>
      <TabPane label="Class C" name="classC">
        <TableComponent
          name="classC"
          :data="data.cycle_count_schedules.schedules_classC"
        ></TableComponent>
      </TabPane>
    </Tabs>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
import TableComponent from "./table.vue";
export default {
  components: {
    PageComponent,
    TableComponent,
  },
  data() {
    return {
      data: {
        cycle_count_schedules: {
          all_schedules: [],
          schedules_classA: [],
          schedules_classB: [],
          schedules_classC: [],
        },
      },
    };
  },
  methods: {},
  async created() {
    const res = await this.callApi(
      "GET",
      "/api/schedules/" + this.$store.getters.getUser.warehouse_id
    );
    console.log(res.data.data);
    this.data.cycle_count_schedules.all_schedules = res.data.data;
    console.log(this.data.cycle_count_schedules.all_schedules);

    this.data.cycle_count_schedules.schedules_classA = _.filter(
      this.data.cycle_count_schedules.all_schedules,
      function (schedule) {
        return schedule.sku.class == "A";
      }
    );
    this.data.cycle_count_schedules.schedules_classB = _.filter(
      this.data.cycle_count_schedules.all_schedules,
      function (schedule) {
        return schedule.sku.class == "B";
      }
    );
    this.data.cycle_count_schedules.schedules_classC = _.filter(
      this.data.cycle_count_schedules.all_schedules,
      function (schedule) {
        return schedule.sku.class == "C";
      }
    );

    $(document).ready(function () {
      $("#classA").DataTable();
    });
    $(document).ready(function () {
      $("#classB").DataTable();
    });
    $(document).ready(function () {
      $("#classC").DataTable();
    });
  },
};
</script>