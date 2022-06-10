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
          v-model="startCycleCountingForm.staffs_assigned"
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
          v-model="startCycleCountingForm.inventories"
          type="textarea"
          disabled
          :autosize="{ minRows: 2, maxRows: 5 }"
          placeholder="Select inventories"
        ></Input>
        <Button>Select inventories</Button>
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
          <!-- <Col span="2" style="text-align: center">to </Col>
          <Col span="5">
            <DatePicker
              v-model="startCycleCountingForm.end_date"
              type="date"
              placement="bottom-end"
              placeholder="End date"
            ></DatePicker>
          </Col> -->
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
          :indeterminate="indeterminate"
          :model-value="checkAll"
          @click.prevent="handleCheckAll"
          >Select All</Checkbox
        >
      </div>
      <CheckboxGroup v-model="checkAllGroup" @on-change="checkAllGroupChange">
        <Checkbox v-for="staff in staffs" :key="staff.id">
          {{ staff.id }}: {{ staff.name }}</Checkbox
        >
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
        staffs_assigned: "",
        inventories: "",
        start_end_date: "",
      },
      minDate: {
        disabledDate(date) {
          return date && date.valueOf() < Date.now() - 86400000;
        },
      },
      staffs: [],
      indeterminate: true,
      checkAll: false,
      checkAllGroup: [],
    };
  },
  methods: {
    assignStaff() {
      console.log(this.staffs);
      this.startCycleCountingForm.staffs_assigned = "";
      _.forEach(this.staffs, (staff) => {
        this.startCycleCountingForm.staffs_assigned += `${staff.id}:${staff.name}`;
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
    handleCheckAll() {
      if (this.indeterminate) {
        this.checkAll = false;
      } else {
        this.checkAll = !this.checkAll;
      }
      this.indeterminate = false;

      if (this.checkAll) {
        this.checkAllGroup = ["香蕉", "苹果", "西瓜"];
      } else {
        this.checkAllGroup = [];
      }
    },
    checkAllGroupChange(data) {
      if (data.length === 3) {
        this.indeterminate = false;
        this.checkAll = true;
      } else if (data.length > 0) {
        this.indeterminate = true;
        this.checkAll = false;
      } else {
        this.indeterminate = false;
        this.checkAll = false;
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