<template>
  <PageComponent title="Start Cycle Counting">
    <Form
      :model="startCycleCountingForm"
      :label-width="120"
      label-position="left"
    >
      <div class="form-label">Working day of warehouse:</div>
      <FormItem label-width="0">
        <Row>
          <Col span="6"
            ><Select
              v-model="startCycleCountingForm.workday_start"
              placeholder="Select Workday Start"
            >
              <Option
                v-for="item in workdays"
                :key="item.value"
                value="item.value"
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
                value="item.value"
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
            value="item.value"
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
            value="item.value"
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
            value="item.value"
            >{{ item.text }}</Option
          >
        </Select>
      </FormItem>
      <div class="form-label">Assign Staff(s):</div>
      <FormItem label-width="0">
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
      <FormItem label-width="0">
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
      <FormItem label-width="0">
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
        <Button>Submit</Button>
      </div>
    </Form>
    <Modal
      v-model="assignStaffModal"
      title="Assign Staff"
      @on-ok="assignStaff"
      @on-cancel="cancel"
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
      @on-cancel="cancel"
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
      workdays: [
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
        {
          value: "sunday",
          text: "Sunday",
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
        start_end_date: "",
      },
      minDate: {
        disabledDate(date) {
          return date && date.valueOf() < Date.now() - 86400000;
        },
      },
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