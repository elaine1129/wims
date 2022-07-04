<template>
  <table :id="name" class="display" style="width: 100%">
    <thead>
      <tr>
        <th>Cycle Count ID</th>
        <th>SKU ID</th>
        <th>Inventory Name</th>
        <th>Warehouse Name</th>
        <th>Staff Name</th>
        <th>Date Counted</th>
        <th>IRA(%)</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="cycle_counting in data" :key="cycle_counting.id">
        <td>
          <a @click="showSummaryReportDetails(cycle_counting)">{{
            cycle_counting.id
          }}</a>
        </td>
        <td>{{ cycle_counting.schedule.sku.id }}</td>
        <td>{{ cycle_counting.schedule.sku.inventory.name }}</td>
        <td>{{ cycle_counting.schedule.sku.inventory.warehouse.name }}</td>
        <td>{{ cycle_counting.schedule.staff.name }}</td>
        <td>{{ convertDate(cycle_counting.created_at) }}</td>
        <td>{{ cycle_counting.inv_rec_accuracy }}</td>
      </tr>
    </tbody>
  </table>
  <Modal v-model="summaryReportModal" title="Summary Report Details">
    <div class="grid grid-cols-3 gap-3">
      <div>SKU ID</div>
      <div>
        {{
          selectedSummaryReport ? selectedSummaryReport.schedule.sku.id : "-"
        }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Cycle Count ID</div>
      <div>
        {{ selectedSummaryReport ? selectedSummaryReport.id : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Inventory ID</div>
      <div>
        {{
          selectedSummaryReport
            ? selectedSummaryReport.schedule.sku.inventory.id
            : "-"
        }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Inventory Name</div>
      <div class="col-span-2">
        {{
          selectedSummaryReport
            ? selectedSummaryReport.schedule.sku.inventory.name
            : "-"
        }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Warehouse ID</div>
      <div>
        {{
          selectedSummaryReport
            ? selectedSummaryReport.schedule.sku.inventory.warehouse.id
            : "-"
        }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Warehouse Name</div>
      <div>
        {{
          selectedSummaryReport
            ? selectedSummaryReport.schedule.sku.inventory.warehouse.name
            : "-"
        }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Staff ID</div>
      <div>
        {{
          selectedSummaryReport ? selectedSummaryReport.schedule.staff.id : "-"
        }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Staff Name</div>
      <div>
        {{
          selectedSummaryReport
            ? selectedSummaryReport.schedule.staff.name
            : "-"
        }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Date Counted</div>
      <div>
        {{
          selectedSummaryReport
            ? convertDate(selectedSummaryReport.created_at)
            : "-"
        }}
      </div>
    </div>
    <Divider size="small" class="italic">Inventory Record Accuracy</Divider>
    <div class="grid grid-cols-3 gap-3">
      <div>Recorded Count</div>
      <div>
        {{ selectedSummaryReport ? selectedSummaryReport.recorded_count : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Actual Count</div>
      <div>
        {{ selectedSummaryReport ? selectedSummaryReport.actual_count : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Variance</div>
      <div>
        {{ selectedSummaryReport ? selectedSummaryReport.variance : "-" }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div>Absolute Variance</div>
      <div>
        {{
          selectedSummaryReport ? Math.abs(selectedSummaryReport.variance) : "-"
        }}
      </div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div class="font-bold">IRA =</div>
      <div class="font-bold">
        {{
          selectedSummaryReport ? selectedSummaryReport.inv_rec_accuracy : "-"
        }}
        %
      </div>
    </div>
    <template #footer>
      <Button type="primary" @click="summaryReportModal = false">OK</Button>
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
      summaryReportModal: false,
      selectedSummaryReport: null,
    };
  },
  methods: {
    showSummaryReportDetails(cycle_counting) {
      this.selectedSummaryReport = cycle_counting;
      console.log(this.selectedSummaryReport);
      this.summaryReportModal = true;
    },
  },
};
</script>