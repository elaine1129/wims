<template>
  <PageComponent title="View Cycle Counting">
    <Tabs value="name1">
      <TabPane label="Class A" name="classA"
        ><table id="classA" class="display" style="width: 100%">
          <thead>
            <tr>
              <th>SKU ID</th>
              <th>Inventory ID</th>
              <th>Inventory Name</th>
              <th>Storage Number</th>
              <th>Schedule</th>
              <th>Days due</th>
              <th>Staff assigned</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </TabPane>
      <TabPane label="Class B" name="classB">标签二的内容</TabPane>
      <TabPane label="Class C" name="classC">标签三的内容</TabPane>
    </Tabs>
  </PageComponent>
</template>

<script>
import PageComponent from "../../components/pages/default-page.vue";

export default {
  components: {
    PageComponent,
  },
  data() {
    return {};
  },
  async created() {
    const res = await this.callApi(
      "GET",
      "/api/schedules/" + this.$store.getters.getUser.warehouse_id
    );
    console.log(res);
  },
};
</script>