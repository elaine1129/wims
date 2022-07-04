<template>
  <PageComponent title="View Staffs">
    <PageHeader :hidden-breadcrumb="true" title="Warehouse Info">
      <template #content>
        <DescriptionList :col="2">
          <Description term="ID " class="headerRow">{{
            data.warehouse ? data.warehouse.id : "-"
          }}</Description>
          <Description term="Name " class="headerRow">{{
            data.warehouse ? data.warehouse.name : "-"
          }}</Description>
          <Description term="Location " class="headerRow">{{
            data.warehouse ? data.warehouse.location : "-"
          }}</Description>
          <Description term="Warehouse Manager " class="headerRow">{{
            data.warehouse ? data.warehouse.manager.name : "-"
          }}</Description>
          <Description term="Created by " class="headerRow">{{
            data.warehouse
              ? data.warehouse.created_at
                ? data.warehouse.created_at
                : "-"
              : "-"
          }}</Description>
          <Description term="Updated by " class="headerRow">{{
            data.warehouse ? data.warehouse.updated_at : "-"
          }}</Description>
        </DescriptionList>
      </template>
    </PageHeader>
    <br />
    <table id="staffs" class="display" style="width: 100%">
      <thead>
        <tr>
          <th>Staff ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Contact No.</th>
          <th>Employed in</th>
          <th>Created at</th>
          <th>Updated at</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="staff in data.staffs" :key="staff.id">
          <td>
            <a @click="showUserDetails(staff)">{{ staff.id }}</a>
          </td>
          <td>{{ staff.name }}</td>
          <td>{{ staff.email }}</td>
          <td>{{ staff.contact_no }}</td>
          <td>{{ staff.employed_in }}</td>
          <td>{{ staff.created_at ? staff.created_at : "-" }}</td>
          <td>{{ staff.updated_at ? staff.updated_at : "-" }}</td>
        </tr>
      </tbody>
    </table>
    <Modal
      v-model="userDetailsModal"
      :title="`View User - ${selectedStaff ? selectedStaff.id : '-'}`"
    >
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">ID</div>
        <div class="col-span-2">
          {{ selectedStaff ? selectedStaff.id : "-" }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">Name</div>
        <div class="col-span-2">
          {{ selectedStaff ? selectedStaff.name : "-" }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">Email</div>
        <div class="col-span-2">
          {{ selectedStaff ? selectedStaff.email : "-" }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">Contact No</div>
        <div class="col-span-2">
          {{ selectedStaff ? selectedStaff.contact_no : "-" }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">IC No</div>
        <div class="col-span-2">
          {{ selectedStaff ? selectedStaff.ic_no : "-" }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">Role</div>
        <div class="col-span-2">
          {{ selectedStaff ? selectedStaff.role : "-" }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">Warehouse</div>
        <div class="col-span-2">
          {{ data.warehouse ? data.warehouse.name : "-" }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">Employed In</div>
        <div class="col-span-2">
          {{ selectedStaff ? selectedStaff.employed_in : "-" }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">Address</div>
        <div class="col-span-2">
          {{ selectedStaff ? selectedStaff.address : "-" }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">Created At</div>
        <div class="col-span-2">
          {{
            selectedStaff
              ? selectedStaff.created_at
                ? selectedStaff.created_at
                : "-"
              : "-"
          }}
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div class="user-details-label">Updated At</div>
        <div class="col-span-2">
          {{
            selectedStaff
              ? selectedStaff.updated_at
                ? selectedStaff.updated_at
                : "-"
              : "-"
          }}
        </div>
      </div>
      <template #footer>
        <Button type="primary" @click="userDetailsModal = false">OK</Button>
      </template>
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
      data: {
        // staffs: [],
        // userData: null,
        warehouse: null,
        staffs: [],
      },
      userDetailsModal: false,
      selectedStaff: null,
    };
  },
  async created() {
    // this.data.userData = this.$store.getters.getUser;
    // console.log(this.data.userData);
    const res = await this.$axiosClient.get(
      "/warehouse/" + this.$store.getters.getUser.warehouse_id
    );
    console.log("warehosue", res);
    this.data.warehouse = res.data.data;
    this.data.staffs = _.filter(res.data.data.staffs, { role: "Staff" });
    console.log(this.data.staffs);
    $(document).ready(function () {
      $("#staffs").DataTable();
    });
  },
  methods: {
    showUserDetails(staff) {
      this.selectedStaff = staff;
      this.userDetailsModal = true;
    },
  },
};
</script>

<style>
.headerRow .ivu-description-term {
  min-width: 150px;
}

.user-details-label {
  font-weight: bold;
}
</style>

