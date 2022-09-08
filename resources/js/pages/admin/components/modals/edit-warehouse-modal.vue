<template>
  <Modal v-model="modal" title="Edit Warehouse">
    <Form :model="editWarehouseForm" :label-width="80">
      <FormItem label="ID">
        <Input :disabled="true" v-model="editWarehouseForm.id"></Input>
      </FormItem>
      <FormItem label="Name">
        <Input :disabled="true" v-model="editWarehouseForm.name"></Input>
      </FormItem>
      <FormItem label="Location">
        <Input
          v-model="editWarehouseForm.location"
          placeholder="Enter location"
        ></Input>
      </FormItem>
      <FormItem label="Warehouse manager">
        <Select v-model="editWarehouseForm.manager.id">
          <Option
            v-for="staff in editWarehouseForm.staffs"
            :key="staff.id"
            :value="staff.id"
            >{{ staff.name }}</Option
          >
        </Select>
      </FormItem>
    </Form>

    <template #footer>
      <Button @click="closeUpdateWarehouseModal">Cancel</Button>
      <Button type="primary" @click="editWarehouse">Confirm</Button>
    </template>
  </Modal>
</template>

<script>
export default {
  data() {
    return {
      data: {
        staff_candidates: [],
      },
      modal: false,
      editWarehouseForm: {
        id: "",
        name: "",
        location: "",
        manager: {
          id: "",
          name: "",
        },
        staffs: [
          {
            id: "",
            name: "",
          },
        ],
      },
    };
  },
  created() {},
  methods: {
    async setProps(modal, selectedWarehouse) {
      console.log(selectedWarehouse);
      this.editWarehouseForm = _.cloneDeep(selectedWarehouse);

      if (this.editWarehouseForm.manager == null) {
        this.editWarehouseForm.manager = {
          id: "",
          name: "",
        };
      }
      this.editWarehouseForm.staffs = _.filter(this.editWarehouseForm.staffs, {
        status: "ACTIVE",
      });
      console.log(selectedWarehouse);
      this.modal = modal;
    },
    async editWarehouse() {
      let params = {
        location: this.editWarehouseForm.location,
        manager_id: this.editWarehouseForm.manager.id,
      };
      await this.$axiosClient
        .put("/warehouse/" + this.editWarehouseForm.id, params)
        .then((response) => {
          this.editWarehouseForm = {
            id: "",
            name: "",
            location: "",
            manager: {
              id: "",
              name: "",
            },
            staffs: [
              {
                id: "",
                name: "",
              },
            ],
          };
          this.success("The warehouse has been successfully updated.");
          this.closeUpdateWarehouseModal();
          this.$emit("edited");
        })
        .catch((error) => {
          this.handleApiError(error);
        });
    },
    closeUpdateWarehouseModal() {
      this.modal = false;
    },
  },
};
</script>