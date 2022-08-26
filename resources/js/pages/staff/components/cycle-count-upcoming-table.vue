<template>
  <table :id="name" class="display" style="width: 100%">
    <thead>
      <tr>
        <th>SKU ID</th>
        <th>Inventory ID</th>
        <th>Inventory Name</th>
        <th>Storage Number</th>
        <th>Schedule</th>
        <th>Days due</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="schedule in data" :key="schedule.id">
        <td>{{ schedule.sku.id }}</td>
        <td>{{ schedule.sku.inventory.id }}</td>
        <td>{{ schedule.sku.inventory.name }}</td>
        <td>
          {{
            schedule.storage_bin[0] ? schedule.storage_bin[0].bin_number : "-"
          }}
        </td>
        <td>{{ schedule.schedule ? schedule.schedule : "-" }}</td>
        <td>
          {{
            schedule.schedule
              ? Math.ceil(
                  (new Date(schedule.schedule) - new Date()) /
                    (1000 * 3600 * 24)
                )
              : "-"
          }}
        </td>
        <td>
          <Button type="primary" @click="countSKU(schedule)">Count</Button>
        </td>
      </tr>
    </tbody>
  </table>
  <Modal v-model="countSKUModal" title="Count SKU">
    <div class="grid">
      <div class="justify-self-center modal-content text-center">
        <Row>
          <Col span="11">Bin Location:</Col>
          <Col span="11">{{
            selectedSchedule
              ? selectedSchedule.storage_bin.length == 0
                ? "No bin assigned! Kindly approach the manager."
                : selectedSchedule.storage_bin[0].bin_number
              : "-"
          }}</Col>
        </Row>
        <Row>
          <Col span="11">System Count:</Col>
          <Col span="11">{{
            selectedSchedule ? selectedSchedule.sku.inventory.qty_on_hand : "-"
          }}</Col>
        </Row>
      </div>
    </div>
    <br />
    <div class="grid">
      <div class="justify-self-center modal-content w-1/2 text-center">
        <Row>
          <Form
            ref="countSKUForm"
            :model="countSKUForm"
            :label-width="150"
            :rules="countSKUFormValidation"
          >
            <FormItem label="Enter actual count" prop="actual_count">
              <Input
                v-model="countSKUForm.actual_count"
                type="number"
                model-value="countSKUForm.actual_count"
              ></Input>
            </FormItem>
          </Form>
        </Row>
      </div>
    </div>
    <template #footer>
      <Button @click="closeCountSKUModal('countSKUForm')">Cancel</Button>
      <Button type="primary" @click="handleSubmit('countSKUForm')">
        Create
      </Button>
    </template>
  </Modal>
</template>


<script>
export default {
  props: {
    data: Array,
    name: String,
  },

  data() {
    return {
      countSKUModal: false,
      selectedSchedule: null,
      countSKUForm: {
        actual_count: 0,
      },
      countSKUFormValidation: {
        actual_count: [
          {
            required: true,
            message: "Please enter a count",
            trigger: "blur",
          },
          {
            type: "number",
            min: 0,
            message: "Please enter a positive value",
            trigger: "blur",
            transform(value) {
              return Number(value);
            },
          },
        ],
      },
    };
  },
  methods: {
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.storeCycleCounting();
          console.log("success", this.countSKUModal);
        } else {
          this.warning("There's something wrong with the input!");
        }
      });
    },
    countSKU(schedule) {
      this.countSKUModal = true;
      this.selectedSchedule = schedule;
      console.log(this.selectedSchedule);
    },
    async storeCycleCounting() {
      console.log(this.selectedSchedule);
      console.log(this.countSKUForm);
      var variance =
        _.parseInt(this.countSKUForm.actual_count) -
        this.selectedSchedule.sku.inventory.qty_on_hand;
      var inv_rec_accuracy =
        1 -
        Math.abs(variance) / this.selectedSchedule.sku.inventory.qty_on_hand;
      var params = {
        schedule_id: this.selectedSchedule.id,
        actual_count: _.parseInt(this.countSKUForm.actual_count),
        recorded_count: this.selectedSchedule.sku.inventory.qty_on_hand,
        variance: variance,
        inv_rec_accuracy: inv_rec_accuracy.toFixed(3),
        status: "PENDING",
      };
      await this.$axiosClient
        .post("/cycle-count", params)
        .then((response) => {
          this.success("The cycle counting has been recorded.");
          this.countSKUModal = false;
          this.countSKUForm = {
            actual_count: 0,
          };
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
    closeCountSKUModal(name) {
      this.countSKUModal = false;
      this.countSKUForm = {
        actual_count: 0,
      };
      this.$refs[name].resetFields();
      console.log(this.countSKUForm);
    },
  },
};
</script>