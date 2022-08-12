<template>
  <PageComponent title="View Cycle Counting">
    <div class="flex justify-between">
      <Button
        id="button"
        type="primary"
        style="margin-bottom: 20px; margin-right: 20px"
        @click="reassignSchedule"
        >Reassign staff</Button
      >
      <Button type="default" @click="currentCCSettingsModal = true"
        >View cycle counting settings</Button
      >
    </div>
    <Modal
      v-model="currentCCSettingsModal"
      title="Current cycle counting settings"
    >
      <div class="grid grid-cols-4">
        <div>Period</div>
        <div class="col-span-3">
          {{
            data.cycle_counting_settings
              ? data.cycle_counting_settings.start_end_date
                ? `${this.convertDate(
                    data.cycle_counting_settings.start_end_date[0]
                  )} - ${this.convertDate(
                    data.cycle_counting_settings.start_end_date[1]
                  )}`
                : "-"
              : "-"
          }}
        </div>
        <div>Working day</div>
        <div class="col-span-3">
          {{
            data.cycle_counting_settings
              ? `${data.cycle_counting_settings.working_day_start} to ${data.cycle_counting_settings.working_day_end}`
              : "-"
          }}
        </div>
        <div>Counting frequency</div>
        <div class="col-span-3">
          <Table
            :columns="countingFrequencyTableHeader"
            :data="countingFrequencyData"
          ></Table>
        </div>
        <div>Warehouse</div>
        <div class="col-span-3">
          {{ data.warehouse ? data.warehouse.name : "-" }}
        </div>
        <div>Assigned Staff</div>
        <div class="col-span-3">
          <a @click="staffsAssignedModal = true">{{
            data.cycle_counting_settings
              ? data.cycle_counting_settings.staff_ids?.length
              : "-"
          }}</a>
        </div>
        <div>No. of SKU</div>
        <div class="col-span-3">
          <a @click="skuModal = true">{{
            data.cycle_counting_settings
              ? data.cycle_counting_settings.inventory_ids?.length
              : "-"
          }}</a>
        </div>
      </div>
      <Table
        :columns="cycle_counting_summary_table_columns"
        :data="data.cycle_counting_settings.cycle_count_class"
        border
        show-summary
        :summary-method="handleSummary"
        height="200"
      ></Table>
      <template #footer>
        <Button type="primary" @click="currentCCSettingsModal = false"
          >OK</Button
        >
      </template>
    </Modal>
    <Modal v-model="staffsAssignedModal" title="View Staff Assigned">
      <table id="staffsAssignedTable" class="display" style="width: 100%">
        <thead>
          <tr>
            <th>Staff ID</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="staff in data.cycle_counting_settings.staff_ids"
            :key="staff.id"
          >
            <td>{{ staff.id }}</td>
            <td>{{ staff.name }}</td>
          </tr>
        </tbody>
      </table>
      <template #footer>
        <Button type="primary" @click="staffsAssignedModal = false">OK</Button>
      </template>
    </Modal>
    <Modal v-model="skuModal" title="View SKU">
      <table id="skuTable" class="display" style="width: 100%">
        <thead>
          <tr>
            <th>SKU ID</th>
            <th>Inventory ID</th>
            <th>Name</th>
            <th>Class</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sku in data.skus" :key="sku.id">
            <td>{{ sku.id }}</td>
            <td>{{ sku.inventory.id }}</td>
            <td>{{ sku.inventory.name }}</td>
            <td>{{ sku.class }}</td>
          </tr>
        </tbody>
      </table>
      <template #footer>
        <Button type="primary" @click="skuModal = false">OK</Button>
      </template>
    </Modal>
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
        cycle_counting_settings: {
          cycle_count_class: [],
          staff_ids: [
            {
              id: "",
              name: "",
            },
          ],
        },
        skus: [],
        warehouse: null,
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
      currentCCSettingsModal: false,
      countingFrequencyTableHeader: [
        {
          title: "Class A",
          key: "classA",
        },
        {
          title: "Class B",
          key: "classB",
        },
        {
          title: "Class C",
          key: "classC",
        },
      ],
      cycle_counting_summary_table_columns: [
        {
          title: "Group",
          key: "class",
        },
        {
          title: "Items",
          key: "number_of_skus",
        },
        {
          title: "Frequency(within date range)",
          key: "frequency",
        },
        {
          title: "Daily Count",
          key: "daily_count",
        },
      ],
      countingFrequencyData: [
        {
          classA: "",
          classB: "",
          classC: "",
        },
      ],
      staffsAssignedModal: false,
      skuModal: false,
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
        this.data.active_users = response.data.data;
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
    handleSummary({ columns, data }) {
      const sums = {};
      columns.forEach((column, index) => {
        const key = column.key;
        if (index === 0) {
          sums[key] = {
            key,
            value: "Total",
          };
          return;
        }
        const values = data.map((item) => {
          if (item["number_of_skus"] === 0) {
            // dont include into sum if the number of skus for that class is 0
            return 0;
          } else {
            return Number(item[key]);
          }
        });
        if (!values.every((value) => isNaN(value))) {
          const v = values.reduce((prev, curr) => {
            const value = Number(curr);
            if (!isNaN(value)) {
              return prev + curr;
            } else {
              return prev;
            }
          }, 0);
          sums[key] = {
            key,
            value: v,
          };
        } else {
          sums[key] = {
            key,
            value: "N/A",
          };
        }
      });

      return sums;
    },
  },
  async created() {
    await this.$axiosClient
      .get("/warehouse/" + this.$store.getters.getUser.warehouse_id)
      .then((response) => {
        console.log(response);
        this.data.cycle_counting_settings =
          response.data.data.cycle_counting_settings;
        this.data.warehouse = response.data.data;
        this.countingFrequencyData[0].classA = this.data.cycle_counting_settings
          .cycle_count_class
          ? `Every ${this.data.cycle_counting_settings.cycle_count_class[0].type_freq} ${this.data.cycle_counting_settings.cycle_count_class[0].type}(s)`
          : "-";
        this.countingFrequencyData[0].classB = this.data.cycle_counting_settings
          .cycle_count_class
          ? `Every ${this.data.cycle_counting_settings.cycle_count_class[1].type_freq} ${this.data.cycle_counting_settings.cycle_count_class[1].type}(s)`
          : "-";
        this.countingFrequencyData[0].classC = this.data.cycle_counting_settings
          .cycle_count_class
          ? `Every ${this.data.cycle_counting_settings.cycle_count_class[2].type_freq} ${this.data.cycle_counting_settings.cycle_count_class[2].type}(s)`
          : "-";

        let staffs = _.map(
          this.data.cycle_counting_settings.staff_ids,
          (staff) => {
            return {
              id: _.split(staff, ":", 2)[0],
              name: _.split(staff, ":", 2)[1],
            };
          }
        );
        this.data.cycle_counting_settings.staff_ids = staffs;
      })
      .catch((error) => {
        this.handleApiError(error);
      });
    await this.$axiosClient
      .get("/skus")
      .then((response) => {
        console.log("skus", response);
        this.data.skus = response.data.data;
      })
      .catch((error) => {
        this.handleApiError(error);
      });
    await this.$axiosClient
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
    $(document).ready(function () {
      $("#staffsAssignedTable").DataTable();
    });
    $(document).ready(function () {
      $("#skuTable").DataTable();
    });
  },
};
</script>

