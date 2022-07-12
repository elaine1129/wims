<template>
  <table :id="name" class="display" style="width: 100%">
    <thead>
      <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Contact No.</th>
        <th v-if="this.$store.getters.getUser.role == 'Admin'">Role</th>
        <th
          v-if="
            this.$store.getters.getUser.role == 'Admin' &&
            this.$route.name != 'view-warehouse-details'
          "
        >
          Warehouse
        </th>

        <th>Employed in</th>
        <th>Created at</th>
        <th>Updated at</th>
        <th
          v-if="
            this.$store.getters.getUser.role == 'Admin' &&
            this.$route.name != 'view-warehouse-details'
          "
        >
          Actions
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="user in data" :key="user.id">
        <td>
          <router-link
            :to="{
              name: 'view-user-details',
              params: { id: user.id },
            }"
            >{{ user.id }}</router-link
          >
        </td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ user.contact_no }}</td>
        <td v-if="this.$store.getters.getUser.role == 'Admin'">
          {{ user.role }}
        </td>
        <td
          v-if="
            this.$store.getters.getUser.role == 'Admin' &&
            this.$route.name != 'view-warehouse-details'
          "
        >
          {{ user.warehouse.name }}
        </td>

        <td>{{ user.employed_in }}</td>
        <td>{{ user.created_at ? user.created_at : "-" }}</td>
        <td>{{ user.updated_at ? user.updated_at : "-" }}</td>
        <td
          v-if="
            this.$store.getters.getUser.role == 'Admin' &&
            this.$route.name != 'view-warehouse-details'
          "
        >
          <div class="flex items-center gap-x-3">
            <Button
              type="success"
              class="basis-1/2"
              @click="showEditUserModal(user)"
              >Edit</Button
            >
            <Button
              type="error"
              class="basis-1/2"
              @click="showDeleteUserModal(user)"
              >Delete</Button
            >
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  <EditUserModalComponent ref="editUserModalComponent"></EditUserModalComponent>
  <DeleteUserModalComponent
    ref="deleteUserModalComponent"
    @deleted="deletedInChild"
  ></DeleteUserModalComponent>
</template>

<script>
import EditUserModalComponent from "../../pages/admin/components/modals/edit-user-modal.vue";
import DeleteUserModalComponent from "../../pages/admin/components/modals/delete-user-modal.vue";
export default {
  props: {
    name: String,
    data: Array,
  },
  components: {
    EditUserModalComponent,
    DeleteUserModalComponent,
  },
  data() {
    return {
      selectedUser: null,
      userDetailsModal: false,
    };
  },
  methods: {
    showUserDetails(staff) {
      this.selectedUser = staff;
      this.userDetailsModal = true;
    },
    showEditUserModal(user) {
      this.selectedUser = user;
      this.$refs.editUserModalComponent.setProps(true, this.selectedUser);
    },
    showDeleteUserModal(user) {
      this.selectedUser = user;
      this.$refs.deleteUserModalComponent.setProps(true, this.selectedUser);
    },
    deletedInChild(id) {
      this.myData = _.remove(this.data, { id: id });
    },
  },
};
</script>