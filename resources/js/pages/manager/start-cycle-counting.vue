<template>
  <PageComponent title="Start Cycle Counting">
    <Form
      :model="startCycleCountingForm"
      :label-width="120"
      label-position="left"
    >
      <div class="form-label">Working day of warehouse:</div>
      <FormItem :label-width="0">
        <Row>
          <Col span="6"
            ><Select
              v-model="startCycleCountingForm.workday_start"
              placeholder="Select Workday Start"
            >
              <Option
                v-for="item in workdays"
                :key="item.value"
                :value="item.value"
                >{{ item.text }}</Option
              >
            </Select>
          </Col>
          <Col span="2" style="text-align: center">to </Col>
          <Col span="6">
            <Select
              v-model="startCycleCountingForm.workday_end"
              placeholder="Select Workday Start"
            >
              <Option
                v-for="item in workdays"
                :key="item.value"
                :value="item.value"
                >{{ item.text }}</Option
              >
            </Select>
          </Col>
        </Row>
      </FormItem>
      <div class="form-label">Counting frequency:</div>
      <FormItem label="Class A: Every">
        <Input
          class="counting-frequency numberType"
          type="number"
          v-model="startCycleCountingForm.classA"
          placeholder="Enter something..."
        ></Input>
        <Select
          class="counting-frequency freqType"
          v-model="startCycleCountingForm.classAType"
          placeholder="Select Workday Start"
        >
          <Option
            v-for="item in cycleCountingFreqType"
            :key="item.value"
            :value="item.value"
            >{{ item.text }}</Option
          >
        </Select>
      </FormItem>
      <FormItem label="Class B: Every">
        <Input
          class="counting-frequency numberType"
          type="number"
          v-model="startCycleCountingForm.classB"
          placeholder="Enter something..."
        ></Input>
        <Select
          class="counting-frequency freqType"
          v-model="startCycleCountingForm.classBType"
          placeholder="Select Workday Start"
        >
          <Option
            v-for="item in cycleCountingFreqType"
            :key="item.value"
            :value="item.value"
            >{{ item.text }}</Option
          >
        </Select>
      </FormItem>
      <FormItem label="Class C: Every">
        <Input
          class="counting-frequency numberType"
          type="number"
          v-model="startCycleCountingForm.classC"
          placeholder="Enter something..."
        ></Input>
        <Select
          class="counting-frequency freqType"
          v-model="startCycleCountingForm.classCType"
          placeholder="Select Workday Start"
        >
          <Option
            v-for="item in cycleCountingFreqType"
            :key="item.value"
            :value="item.value"
            >{{ item.text }}</Option
          >
        </Select>
      </FormItem>
      <div class="form-label">Assign Staff(s):</div>
      <FormItem :label-width="0">
        <Input
          v-model="startCycleCountingForm.staffs_assigned_str"
          type="textarea"
          disabled
          :autosize="{ minRows: 2, maxRows: 5 }"
          placeholder="Assign staff"
        ></Input>
        <Button @click="getStaffs">Assign Staff</Button>
      </FormItem>
      <div class="form-label">Select Inventories(s):</div>
      <FormItem :label-width="0">
        <Input
          v-model="startCycleCountingForm.inventories_str"
          type="textarea"
          disabled
          :autosize="{ minRows: 2, maxRows: 5 }"
          placeholder="Select inventories"
        ></Input>
        <Button @click="getInvs">Select inventories</Button>
      </FormItem>
      <div class="form-label">Start and End date:</div>
      <FormItem :label-width="0">
        <Row>
          <Col span="5">
            <DatePicker
              v-model="startCycleCountingForm.start_end_date"
              type="daterange"
              :options="minDate"
              placement="bottom-end"
              placeholder="Start and End date"
            ></DatePicker>
          </Col>
        </Row>
      </FormItem>
      <div class="flex justify-center">
        <Button type="error">Cancel</Button>
        <Button @click="startCycleCounting">Submit</Button>
      </div>
    </Form>
    <Modal
      v-model="assignStaffModal"
      title="Assign Staff"
      @on-ok="assignStaff"
      @on-cancel="closeAssignStaffModal"
    >
      <div
        style="
          border-bottom: 1px solid #e9e9e9;
          padding-bottom: 6px;
          margin-bottom: 6px;
        "
      >
        <Checkbox
          :indeterminate="staff_indeterminate"
          :model-value="staff_checkAll"
          @click.prevent="staffHandleCheckAll"
          >Select All</Checkbox
        >
      </div>
      <CheckboxGroup
        v-model="startCycleCountingForm.staffs_assigned"
        @on-change="staffCheckAllGroupChange"
      >
        <Checkbox
          v-for="staff in staffs"
          :key="staff.id"
          :label="`${staff.id}:${staff.name}`"
        >
        </Checkbox>
      </CheckboxGroup>
    </Modal>
    <Modal
      v-model="selectInvModal"
      title="Select Inventory"
      @on-ok="selectInv"
      @on-cancel="closeSelectInvModal"
    >
      <div
        style="
          border-bottom: 1px solid #e9e9e9;
          padding-bottom: 6px;
          margin-bottom: 6px;
        "
      >
        <Checkbox
          :indeterminate="inv_indeterminate"
          :model-value="inv_checkAll"
          @click.prevent="invHandleCheckAll"
          >Select All</Checkbox
        >
      </div>
      <CheckboxGroup
        v-model="startCycleCountingForm.inventories"
        @on-change="invCheckAllGroupChange"
      >
        <Checkbox
          v-for="inventory in inventories"
          :key="inventory.id"
          :label="`${inventory.id}:${inventory.name}`"
        >
        </Checkbox>
      </CheckboxGroup>
    </Modal>
    <Modal
      v-model="confirmStartCycleCountingModal"
      title="Are you sure to start the cycle counting with the following information?"
      @on-ok="createCycleCountSchedule"
      @on-cancel="confirmStartCycleCountingModal = false"
    >
      <Row>
        <Col span="5">
          <div>No. of staff:</div>
        </Col>
        <Col span="5">
          <div>{{ startCycleCountingForm.staffs_assigned.length }}</div>
        </Col>
        <Col span="14" class="text-right">
          <Icon type="ios-calendar-outline" />
          {{ startCycleCountingForm.start_date }} -
          {{ startCycleCountingForm.end_date }}
        </Col>
      </Row>
      <Row>
        <Col span="5">
          <div>No. of SKU(s):</div>
        </Col>
        <Col span="5">
          <div>{{ startCycleCountingForm.sku_list.length }}</div>
        </Col>
      </Row>

      <Table
        :columns="confirm_start_cycle_counting_table_columns"
        :data="startCycleCountingForm.cycle_count_class"
        border
        show-summary
        :summary-method="handleSummary"
        height="200"
      ></Table>
    </Modal>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";
export default {
  components: {
    PageComponent,
  },
  data() {
    return {
      assignStaffModal: false,
      selectInvModal: false,
      confirmStartCycleCountingModal: false,
      workdays: [
        {
          value: "sunday",
          text: "Sunday",
        },
        {
          value: "monday",
          text: "Monday",
        },
        {
          value: "tuesday",
          text: "Tuesday",
        },
        {
          value: "wednesday",
          text: "Wednesday",
        },
        {
          value: "thursday",
          text: "Thursday",
        },
        {
          value: "friday",
          text: "Friday",
        },
        {
          value: "saturday",
          text: "Saturday",
        },
      ],
      cycleCountingFreqType: [
        {
          value: "day",
          text: "Day(s)",
        },
        {
          value: "week",
          text: "Week(s)",
        },
        {
          value: "month",
          text: "Month(s)",
        },
        {
          value: "year",
          text: "Year(s)",
        },
      ],
      startCycleCountingForm: {
        workday_start: "",
        workday_end: "",
        classA: 0,
        classAType: "",
        classB: 0,
        classBType: "",
        classC: 0,
        classCType: "",
        staffs_assigned: [],
        staffs_assigned_str: "",
        inventories: [],
        inventories_str: "",
        start_end_date: [],
        start_date: "",
        end_date: "",
        sku_list: [],
        cycle_count_class: [
          {
            class: "A",
            number_of_skus: 0,
            frequency: 0,
            daily_count: 0,
          },
          {
            class: "B",
            number_of_skus: 0,
            frequency: 0,
            daily_count: 0,
          },
          {
            class: "C",
            number_of_skus: 0,
            frequency: 0,
            daily_count: 0,
          },
        ],
      },
      minDate: {
        disabledDate(date) {
          return date && date.valueOf() < Date.now() - 86400000;
        },
      },
      confirm_start_cycle_counting_table_columns: [
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
          title: "Daily Count(per working day)",
          key: "daily_count",
        },
      ],
      staffs: [],
      inventories: [],
      staff_indeterminate: true,
      staff_checkAll: false,
      checkAllGroup: [],
      inv_indeterminate: true,
      inv_checkAll: false,
      inv_checkAllGroup: [],
    };
  },
  methods: {
    assignStaff() {
      this.startCycleCountingForm.staffs_assigned_str = "";

      this.startCycleCountingForm.staffs_assigned = _.sortBy(
        this.startCycleCountingForm.staffs_assigned,
        (d) => {
          return _.parseInt(_.split(d, ":", 1)[0]);
        }
      );
      _.forEach(this.startCycleCountingForm.staffs_assigned, (staff) => {
        if (this.startCycleCountingForm.staffs_assigned_str.length > 0) {
          this.startCycleCountingForm.staffs_assigned_str += `, ${staff}`;
        } else {
          this.startCycleCountingForm.staffs_assigned_str += staff;
        }
      });
    },
    async getStaffs() {
      const res = await this.callApi(
        "GET",
        `/api/getStaffByWarehouse/${this.$store.getters.getUser.warehouse_id}`
      );
      if (res.status == 200) {
        this.staffs = res.data;
        this.assignStaffModal = true;
      } else {
        this.smtgWentWrong();
      }
    },
    staffHandleCheckAll() {
      if (this.staff_indeterminate) {
        this.staff_checkAll = false;
      } else {
        this.staff_checkAll = !this.staff_checkAll;
      }
      this.staff_indeterminate = false;

      if (this.staff_checkAll) {
        this.startCycleCountingForm.staffs_assigned = _.map(
          this.staffs,
          (staff) => {
            return `${staff.id}:${staff.name}`;
          }
        );
      } else {
        this.startCycleCountingForm.staffs_assigned = [];
      }
    },
    staffCheckAllGroupChange(data) {
      if (data.length === this.staffs.length) {
        this.staff_indeterminate = false;
        this.staff_checkAll = true;
      } else if (data.length > 0) {
        this.staff_indeterminate = true;
        this.staff_checkAll = false;
      } else {
        this.staff_indeterminate = false;
        this.staff_checkAll = false;
      }
    },
    selectInv() {
      this.startCycleCountingForm.inventories_str = "";

      this.startCycleCountingForm.inventories = _.sortBy(
        this.startCycleCountingForm.inventories,
        (d) => {
          return _.parseInt(_.split(d, ":", 1)[0]);
        }
      );
      _.forEach(this.startCycleCountingForm.inventories, (inventory) => {
        if (this.startCycleCountingForm.inventories_str.length > 0) {
          this.startCycleCountingForm.inventories_str += `, ${inventory}`;
        } else {
          this.startCycleCountingForm.inventories_str += inventory;
        }
      });
      console.log(this.startCycleCountingForm.inventories_str);
    },
    async getInvs() {
      const res = await this.callApi(
        "GET",
        `/api/getInvByWarehouse/${this.$store.getters.getUser.warehouse_id}`
      );
      if (res.status == 200) {
        this.inventories = res.data.data;
        this.selectInvModal = true;
      } else {
        this.smtgWentWrong();
      }
    },
    invHandleCheckAll() {
      if (this.inv_indeterminate) {
        this.inv_checkAll = false;
      } else {
        this.inv_checkAll = !this.inv_checkAll;
      }
      this.inv_indeterminate = false;

      if (this.inv_checkAll) {
        this.startCycleCountingForm.inventories = _.map(
          this.inventories,
          (inventory) => {
            return `${inventory.id}:${inventory.name}`;
          }
        );
      } else {
        this.startCycleCountingForm.inventories = [];
      }
    },
    invCheckAllGroupChange(data) {
      if (data.length === this.inventories.length) {
        this.inv_indeterminate = false;
        this.inv_checkAll = true;
      } else if (data.length > 0) {
        this.inv_indeterminate = true;
        this.inv_checkAll = false;
      } else {
        this.inv_indeterminate = false;
        this.inv_checkAll = false;
      }
    },
    closeAssignStaffModal() {
      this.assignStaffModal = false;
    },
    closeSelectInvModal() {
      this.selectInvModal = false;
    },
    startCycleCounting() {
      this.classifySKU();
      this.calculateFrequency_DailyCount();
      console.log("after process: ", this.startCycleCountingForm);
      this.confirmStartCycleCountingModal = true;
    },
    getDailyCount(number_of_skus, freq) {
      let total_working_days = this.calculateTotalWorkingDays(
        this.startCycleCountingForm.start_end_date[0],
        this.startCycleCountingForm.start_end_date[1],
        this.startCycleCountingForm.workday_start,
        this.startCycleCountingForm.workday_end
      );
      return ((number_of_skus * freq) / total_working_days).toFixed(2);
    },
    classifySKU() {
      console.log(this.startCycleCountingForm);

      let stock_values = [];
      _.forEach(this.inventories, (inventory) => {
        stock_values.push(inventory.cost_per_unit * inventory.qty_on_hand);
      });
      let max = _.max(stock_values);
      let min = _.min(stock_values);
      let skulist = [];
      _.forEach(this.startCycleCountingForm.inventories, (inventory) => {
        let sku = {};
        let id = _.parseInt(_.split(inventory, ":", 1)[0]);
        let tempInv = _.find(this.inventories, (inventory) => {
          return inventory.id == id;
        });
        sku.inventory = tempInv;
        sku.stock_value = tempInv.cost_per_unit * tempInv.qty_on_hand;
        sku.transformed_stock_value = _.round(
          (sku.stock_value - min) / (max - min),
          2
        );
        if (sku.transformed_stock_value <= 0.8) {
          sku.class = "A";
        } else if (sku.transformed_stock_value <= 0.95) {
          sku.class = "B";
        } else {
          sku.class = "C";
        }
        skulist.push(sku);
      });
      this.startCycleCountingForm.sku_list = skulist;
    },
    calculateFrequency_DailyCount() {
      this.startCycleCountingForm.start_date = this.convertDate(
        this.startCycleCountingForm.start_end_date[0]
      );
      this.startCycleCountingForm.end_date = this.convertDate(
        this.startCycleCountingForm.start_end_date[1]
      );
      _.forEach(this.startCycleCountingForm.cycle_count_class, (c) => {
        _.forEach(this.startCycleCountingForm.sku_list, (sku) => {
          if (c.class == sku.class) {
            c.number_of_skus += 1;
          }
        });
        if (c.class == "A") {
          c.frequency = this.countFreqPerClass(
            this.startCycleCountingForm.start_end_date[0],
            this.startCycleCountingForm.start_end_date[1],
            this.startCycleCountingForm.workday_start,
            this.startCycleCountingForm.workday_end,
            this.startCycleCountingForm.classA,
            this.startCycleCountingForm.classAType
          );
        } else if (c.class == "B") {
          c.frequency = this.countFreqPerClass(
            this.startCycleCountingForm.start_end_date[0],
            this.startCycleCountingForm.start_end_date[1],
            this.startCycleCountingForm.workday_start,
            this.startCycleCountingForm.workday_end,
            this.startCycleCountingForm.classB,
            this.startCycleCountingForm.classBType
          );
        } else {
          c.frequency = this.countFreqPerClass(
            this.startCycleCountingForm.start_end_date[0],
            this.startCycleCountingForm.start_end_date[1],
            this.startCycleCountingForm.workday_start,
            this.startCycleCountingForm.workday_end,
            this.startCycleCountingForm.classC,
            this.startCycleCountingForm.classCType
          );
        }
        c.daily_count = this.getDailyCount(c.number_of_skus, c.frequency);
      });
    },
    createCycleCountSchedule() {
      this.confirmStartCycleCountingModal = false;
      console.log("confirmed");
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
        const values = data.map((item) => Number(item[key]));
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
  created() {},
  mounted() {
    console.log(this.startCycleCountingForm);
  },
};
</script>

<style scoped>
.form-label {
  font-size: 15px;
  font-weight: bold;
}
.ivu-form-item-content .counting-frequency.numberType {
  width: 80px;
}
.ivu-form-item-content .counting-frequency.freqType {
  width: 150px;
}
</style>