<template>
  <PageComponent title="View Cycle Counting">
    <Button
      id="button"
      type="primary"
      style="margin-bottom: 20px"
      @click="reassignSchedule"
      >Reassign staff</Button
    >
    <Modal v-model="reassignScheduleModal" title="Reassign staff">
      <Form :model="reassignScheduleForm" :label-width="80">
        <FormItem label="Select staff to replace">
          <Select
            v-model="reassignScheduleForm.ori_staff"
            @on-change="processStaff"
          >
            <Option
              v-for="staff in data.schedule_staff"
              :key="staff.id"
              :value="staff.id"
              >{{ staff.name }}</Option
            >
          </Select>
        </FormItem>
        <div v-if="showSummary">
          This staff is currently holding
          {{
            reassignScheduleForm.schedules
              ? reassignScheduleForm.schedules.length
              : "-"
          }}
          schedules
        </div>
        <FormItem label="Select staff to assign">
          <Select v-model="reassignScheduleForm.new_staff">
            <Option
              v-for="staff in data.active_users"
              :key="staff.id"
              :value="staff.id"
              >{{ staff.name }}</Option
            >
          </Select>
        </FormItem>
      </Form>
      <template #footer>
        <Button>Cancel</Button>
        <Button type="primary" @click="confirmReassign">Reassign</Button>
      </template>
    </Modal>

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
import axiosClient from "../../axios";
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
        active_users: [],
        schedule_staff: [],
      },
      reassignScheduleModal: false,
      reassignScheduleForm: {
        ori_staff: {
          id: "",
        },
        schedules: [],
        new_staff: "",
      },
      showSummary: false,
      filtered_schedule: [],
    };
  },
  methods: {
    async reassignSchedule() {
      var unique_staff = _.chain(this.data.cycle_count_schedules.all_schedules)
        .map((schedule) => {
          return {
            ...schedule,
            staff_id: schedule.staff.id,
          };
        })
        .uniqBy("staff_id")
        .value();
      this.data.schedule_staff = _.map(unique_staff, (schedule) => {
        return schedule.staff;
      });
      console.log(this.data.schedule_staff);
      await this.$axiosClient.get("/active-staffs").then((response) => {
        this.data.active_users = response.data;
        console.log(this.data.active_users);
      });
      this.reassignScheduleModal = true;
    },
    processStaff() {
      this.reassignScheduleForm.schedules = _.chain(
        this.data.cycle_count_schedules.all_schedules
      )
        .filter((schedule) => {
          return schedule.staff.id == this.reassignScheduleForm.ori_staff;
        })
        .map((schedule) => {
          return schedule.id;
        })
        .value();
      this.showSummary = true;
    },
    async confirmReassign() {
      console.log(this.reassignScheduleForm);
      await this.$axiosClient
        .post("/reassignStaff", this.reassignScheduleForm)
        .then((response) => {
          this.reassignScheduleForm = {
            ori_staff: {
              id: "",
            },
            schedules: [],
            new_staff: "",
          };
          this.reassignScheduleModal = false;
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
  },
  async created() {
    await axiosClient
      .get("/schedules")
      .then((res) => {
        console.log(res.data.data);
        this.data.cycle_count_schedules.all_schedules = res.data.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });

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
      $("#classA").DataTable({
        order: [[5, "asc"]],
      });
    });
    $(document).ready(function () {
      $("#classB").DataTable({
        order: [[5, "asc"]],
      });
    });
    $(document).ready(function () {
      $("#classC").DataTable({
        order: [[5, "asc"]],
      });
    });
  },
};
</script>

