<script>
    const exportExcel = async () => {
        if (confirm('Apakah Anda yakin?')) {
            let token = @json(json_decode(Cookie::get(config('auth.cookie')))).access_token;
            let {
                data
            } = await axios.get(@json(route('finance::report.salaries.excel', ['start_at' => $start_at->format('Y-m-d'), 'end_at' => $end_at->format('Y-m-d')])), {
                headers: {
                    "Authorization": `Bearer ${token}`
                }
            });
            const wb = new ExcelJS.Workbook();
            const ws = wb.addWorksheet('Rekap gaji karyawan');
            // Add title
            ws.insertRow(1, [data.title]);
            let titleRow = ws.getRow(1);
            titleRow.font = {
                size: 16,
                bold: true
            };
            titleRow.alignment = {
                horizontal: 'center',
                vertical: 'middle'
            };


            // Add subtitle
            ws.insertRow(2, [data.subtitle]);
            let title2Row = ws.getRow(2);
            title2Row.font = {
                size: 16,
                bold: true
            };
            title2Row.alignment = {
                horizontal: 'center',
                vertical: 'middle'
            };
            // Add columns
            row = ws.getRow(3);
            row.getCell(1).value = 'No';
            row.getCell(1).alignment = {
                vertical: 'middle',
                horizontal: 'center'
            };
            ws.mergeCells('A3:A5');
            row.getCell(2).value = 'Nama';
            row.getCell(2).alignment = {
                vertical: 'middle',
                horizontal: 'center'
            };
            ws.mergeCells('B3:B5');
            row.getCell(3).value = 'NPWP';
            row.getCell(3).alignment = {
                vertical: 'middle',
                horizontal: 'center'
            };
            ws.mergeCells('C3:C5');
            row.getCell(4).value = 'Departemen';
            row.getCell(4).alignment = {
                vertical: 'middle',
                horizontal: 'center'
            };
            ws.mergeCells('D3:D5');
            row.getCell(5).value = 'Jabatan';
            row.getCell(5).alignment = {
                vertical: 'middle',
                horizontal: 'center'
            };
            ws.mergeCells('E3:E5');
            let cellnum = 6;
            row = ws.getRow(3);
            row.font = {
                bold: true
            };
            row.getCell(cellnum).value = 'Rekapitulasi';
            row.getCell(cellnum).alignment = {
                vertical: 'middle',
                horizontal: 'center'
            };
            Object.keys(data.attendances).forEach((recap, i) => {
                row = ws.getRow(4);
                row.font = {
                    bold: true
                };
                row.getCell(cellnum).value = recap;

                row.getCell(cellnum).alignment = {
                    vertical: 'middle',
                    horizontal: 'center'
                };
                ws.mergeCells(4, cellnum, 4, cellnum + Object.keys(data.attendances[recap]).length - 1);
                Object.keys(data.attendances[recap]).forEach((item, j) => {
                    row = ws.getRow(5);
                    row.font = {
                        bold: true
                    };
                    row.getCell(cellnum + j).value = item;
                    row.getCell(cellnum + j).alignment = {
                        vertical: 'middle',
                        horizontal: 'center'
                    };
                });
                cellnum += Object.keys(data.attendances[recap]).length;
            });
            ws.mergeCells(3, 6, 3, cellnum - 1);
            Object.keys(data.components).forEach((slip, i) => {
                row = ws.getRow(3);
                row.getCell(cellnum).value = slip;
                row.getCell(cellnum).alignment = {
                    vertical: 'middle',
                    horizontal: 'center'
                };
                let firstcell = cellnum;
                Object.keys(data.components[slip]).forEach((ctg, j) => {
                    row = ws.getRow(4);
                    row.getCell(cellnum).value = ctg;
                    row.getCell(cellnum).alignment = {
                        vertical: 'middle',
                        horizontal: 'center'
                    };
                    ws.mergeCells(4, cellnum, 4, cellnum + Object.keys(data.components[slip][ctg]).length - 1);
                    Object.values(data.components[slip][ctg]).forEach((item, k) => {
                        row = ws.getRow(5);
                        row.getCell(cellnum + k).value = item.name;
                        row.getCell(cellnum + k).alignment = {
                            vertical: 'middle',
                            horizontal: 'center'
                        };
                    });
                    cellnum += data.components[slip][ctg].length;
                });
                ws.mergeCells(3, firstcell, 3, cellnum - 1);
            });
            // Records
            let rownum = 6;
            data.employees.forEach((employee, i) => {
                row = ws.getRow(rownum++);
                row.getCell(1).value = i + 1;
                row.getCell(1).alignment = {
                    vertical: 'middle',
                    horizontal: 'center'
                };
                row.getCell(2).value = employee.name;
                row.getCell(2).alignment = {
                    vertical: 'middle',
                    horizontal: 'center'
                };
                row.getCell(3).value = employee.npwp;
                row.getCell(3).alignment = {
                    vertical: 'middle',
                    horizontal: 'center'
                };
                row.getCell(4).value = employee.department;
                row.getCell(4).alignment = {
                    vertical: 'middle',
                    horizontal: 'center'
                };
                row.getCell(5).value = employee.position;
                row.getCell(5).alignment = {
                    vertical: 'middle',
                    horizontal: 'center'
                };
                employee.attendances.forEach((attendance, j) => {
                    row.getCell(6 + j).value = attendance;
                });
                employee.components.forEach((component, j) => {
                    row.getCell(6 + employee.attendances.length + j).value = component;
                });
                row.getCell(cellnum).value = employee.total;
            });
            ws.mergeCells(1, 1, 1, cellnum - 1);
            ws.mergeCells(2, 1, 2, cellnum - 1);
            // Set total cell
            row = ws.getRow(3);
            row.getCell(cellnum).value = 'Total';
            row.getCell(cellnum).alignment = {
                vertical: 'middle',
                horizontal: 'center'
            };
            ws.mergeCells(3, cellnum, 5, cellnum);
            // Render
            wb.xlsx.writeBuffer().then((file) => {
                const blob = new Blob([file], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8'
                });
                saveAs(blob, `${data.file}.xlsx`);
            });
        }
    }
</script>
