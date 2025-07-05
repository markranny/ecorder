<script setup>
import { saveAs } from 'file-saver';
import ExcelJS from 'exceljs';
import SuccessButton from '@/Components/Buttons/SuccessButton.vue';
import ExcelIcon from "@/Components/Svgs/Excel.vue";

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
    headers: {
        type: Array,
        required: true,
    },
    fileName: {
      type: String,
      default: 'data.xlsx'
    },
    rowNameProps: {
      type: Array,
      required: true,
    }
});
const exportToExcel = async () => {
  try {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Sheet1');

    // Add table headers
    const headers = props.headers;
    worksheet.addRow(headers);

    // Add table data
    props.data.forEach(datum => {
        const row = getRows(datum);
        worksheet.addRow(row);
    });

    // Generate Excel file
    const buffer = await workbook.xlsx.writeBuffer();

    // Save the Excel file
    saveAs(new Blob([buffer]), props.fileName);
  } catch (error) {
    console.error('Error exporting to Excel:', error);
  }
};

const getRows = (datum) => {
  const arrayOfData = [];
  for (const rowNameProp of props.rowNameProps) {
    if (datum[rowNameProp]) {
      arrayOfData.push(datum[rowNameProp]);
    } else {
      return '';
    }
  }
  return arrayOfData;
}
</script>

<template>
    <SuccessButton type="button" @click="exportToExcel">
        <ExcelIcon class="h-4"></ExcelIcon>
    </SuccessButton>
</template>
