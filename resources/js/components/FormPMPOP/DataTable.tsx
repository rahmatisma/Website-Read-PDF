import { Badge } from '@/components/ui/badge';
import React from 'react';

interface TableColumn {
    key: string;
    label: string;
    width?: string;
}

interface DataTableProps {
    columns: TableColumn[];
    data: any[];
    nested?: boolean;
    nestedKey?: string;
    title?: string;
}

export const DataTable: React.FC<DataTableProps> = ({ 
    columns, 
    data, 
    nested = false, 
    nestedKey = 'checklist', 
    title 
}) => {
    if (!data || data.length === 0) return null;

    /**
     *  SMART NESTED KEY DETECTION
     * Automatically detect if item has 'checklist' or 'capacity_options'
     */
    const getNestedKey = (item: any): string | null => {
        if (item.checklist && Array.isArray(item.checklist)) return 'checklist';
        if (item.capacity_options && Array.isArray(item.capacity_options)) return 'capacity_options';
        return null;
    };

    /**
     *  RENDER CELL with smart value detection
     */
    const renderCell = (item: any, column: TableColumn, isNested: boolean = false): React.ReactNode => {
        const value = item[column.key];

        // Handle status badge
        if (column.key === 'status') {
            if (!value) return <span className="text-muted-foreground">-</span>;
            
            return (
                <Badge
                    variant="outline"
                    className={
                        value === 'OK' || value === 'ok' || value === 'Ok'
                            ? 'border-green-500 text-green-400'
                            : value === 'NOK' || value === 'nok'
                            ? 'border-red-500 text-red-400'
                            : 'border-yellow-500 text-yellow-400'
                    }
                >
                    {value}
                </Badge>
            );
        }

        // Handle standard/operational_standard column
        if (column.key === 'standard' || column.key === 'operational_standard') {
            // Try both keys
            const standardValue = item['standard'] || item['operational_standard'];
            
            if (!standardValue) return <span className="text-muted-foreground">-</span>;
            
            return <span className="text-sm">{standardValue}</span>;
        }

        // Handle capacity (for nested items in Inverter/Rectifier)
        if (column.key === 'result' && isNested && item.capacity) {
            // For nested capacity items, show capacity label
            return (
                <span className="text-sm font-medium">
                    {item.capacity}
                </span>
            );
        }

        // Regular value
        if (value === null || value === undefined || value === '') {
            return <span className="text-muted-foreground">-</span>;
        }

        return <span className="text-sm">{String(value)}</span>;
    };

    /**
     *  RENDER NESTED ROW
     * Handle both 'checklist' and 'capacity_options'
     * 
     * Special Cases:
     * 1. Shelter room_temperature: First row shows no & description, nested rows show empty
     * 2. Rectifier capacity_options: Status goes to nested row that matches capacity_detected
     */
    const renderNestedRows = (item: any, itemIndex: number) => {
        const detectedKey = getNestedKey(item);
        
        if (!detectedKey || !item[detectedKey] || !Array.isArray(item[detectedKey])) {
            return null;
        }

        const nestedItems = item[detectedKey];

        return (
            <>
                {nestedItems.map((nestedItem: any, nestedIndex: number) => {
                    //  CHECK: Is this the row that matches capacity_detected?
                    const isMatchingCapacity = item.capacity_detected && 
                                              nestedItem.capacity && 
                                              nestedItem.capacity.toLowerCase().includes(item.capacity_detected.toLowerCase());

                    return (
                        <tr key={`${itemIndex}-${nestedIndex}`} className="bg-muted/10">
                            {columns.map((col, colIndex) => {
                                // First column (No): show indent arrow for nested rows
                                if (colIndex === 0) {
                                    return (
                                        <td key={col.key} className="py-2 pl-6 text-muted-foreground">
                                            ↳
                                        </td>
                                    );
                                }

                                //  FIXED: Second column (Description) for capacity_options and regular checklist
                                if (colIndex === 1) {
                                    let displayText = '';
                                    
                                    // For capacity_options (Rectifier) - SHOULD BE EMPTY
                                    if (detectedKey === 'capacity_options') {
                                        displayText = '-';
                                    }
                                    // For regular checklist (Shelter Room Temperature) - SHOULD BE EMPTY
                                    else if (detectedKey === 'checklist') {
                                        displayText = '-';
                                    }
                                    // For checklist with capacity (Inverter)
                                    else if (nestedItem.capacity) {
                                        displayText = nestedItem.capacity;
                                    }
                                    // Fallback
                                    else {
                                        displayText = nestedItem[col.key] || '-';
                                    }

                                    return (
                                        <td key={col.key} className="py-2 text-muted-foreground">
                                            <span className="text-sm">{displayText}</span>
                                        </td>
                                    );
                                }

                                //  FIXED: Result column for capacity_options
                                if (col.key === 'result') {
                                    // For capacity_options, show parent's result ONLY on matching capacity row
                                    if (detectedKey === 'capacity_options') {
                                        if (isMatchingCapacity && item.result) {
                                            return (
                                                <td key={col.key} className="py-2">
                                                    <span className="text-sm">{item.result}</span>
                                                </td>
                                            );
                                        }
                                        
                                        // Otherwise empty
                                        return (
                                            <td key={col.key} className="py-2 text-muted-foreground">
                                                -
                                            </td>
                                        );
                                    }
                                    
                                    // For other nested rows, render normally
                                    return (
                                        <td key={col.key} className="py-2">
                                            {renderCell(nestedItem, col, true)}
                                        </td>
                                    );
                                }

                                //  FIXED: Standard column for capacity_options - SHOW THE VALUE HERE!
                                if ((col.key === 'standard' || col.key === 'operational_standard') && detectedKey === 'capacity_options') {
                                    // This is where we show: "25 A ( Single Power Module )"
                                    const displayValue = `${nestedItem.standard} ( ${nestedItem.capacity} )`;
                                    
                                    return (
                                        <td key={col.key} className="py-2">
                                            <span className="text-sm">{displayValue}</span>
                                        </td>
                                    );
                                }

                                // For regular checklist, standard is rendered normally
                                if ((col.key === 'standard' || col.key === 'operational_standard') && detectedKey === 'checklist') {
                                    return (
                                        <td key={col.key} className="py-2">
                                            {renderCell(nestedItem, col, true)}
                                        </td>
                                    );
                                }

                                //  STATUS COLUMN - SPECIAL LOGIC FOR CAPACITY_OPTIONS
                                if (col.key === 'status' && detectedKey === 'capacity_options') {
                                    // If this nested row matches capacity_detected, show parent's status
                                    if (isMatchingCapacity && item.status) {
                                        return (
                                            <td key={col.key} className="py-2">
                                                <Badge
                                                    variant="outline"
                                                    className="border-green-500 text-green-400"
                                                >
                                                    {item.status}
                                                </Badge>
                                            </td>
                                        );
                                    }
                                    
                                    // Otherwise, show empty
                                    return (
                                        <td key={col.key} className="py-2 text-muted-foreground">
                                            -
                                        </td>
                                    );
                                }

                                // For regular checklist, status is rendered normally
                                if (col.key === 'status' && detectedKey === 'checklist') {
                                    return (
                                        <td key={col.key} className="py-2">
                                            {renderCell(nestedItem, col, true)}
                                        </td>
                                    );
                                }

                                // Other columns: render normally
                                return (
                                    <td key={col.key} className="py-2">
                                        {renderCell(nestedItem, col, true)}
                                    </td>
                                );
                            })}
                        </tr>
                    );
                })}
            </>
        );
    };

    return (
        <div className="overflow-x-auto">
            {title && <h4 className="mb-3 font-semibold text-blue-400">{title}</h4>}
            
            <table className="w-full text-sm border-collapse">
                <thead>
                    <tr className="border-b-2">
                        {columns.map((col) => (
                            <th 
                                key={col.key} 
                                className="pb-2 pt-2 text-left font-semibold" 
                                style={{ width: col.width }}
                            >
                                {col.label}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {data.map((item, itemIndex) => {
                        const hasNested = nested && getNestedKey(item) !== null;
                        const detectedKey = getNestedKey(item);

                        return (
                            <React.Fragment key={itemIndex}>
                                {/* Main Row */}
                                <tr className="border-b hover:bg-muted/30 transition-colors">
                                    {columns.map((col, colIndex) => {
                                        //  SPECIAL CASE: Result column for items with capacity_options
                                        // Don't show result in parent row if it should be in nested row
                                        if (col.key === 'result' && detectedKey === 'capacity_options' && item.capacity_detected) {
                                            return (
                                                <td key={col.key} className="py-3 text-muted-foreground">
                                                    -
                                                </td>
                                            );
                                        }

                                        //  SPECIAL CASE: Standard column for items with capacity_options
                                        // Don't show standard in parent row, it's in nested rows
                                        if ((col.key === 'standard' || col.key === 'operational_standard') && detectedKey === 'capacity_options') {
                                            return (
                                                <td key={col.key} className="py-3 text-muted-foreground">
                                                    -
                                                </td>
                                            );
                                        }

                                        //  SPECIAL CASE: Status column for items with capacity_detected
                                        // Don't show status in parent row if it should be in nested row
                                        if (col.key === 'status' && detectedKey === 'capacity_options' && item.capacity_detected) {
                                            return (
                                                <td key={col.key} className="py-3 text-muted-foreground">
                                                    -
                                                </td>
                                            );
                                        }

                                        return (
                                            <td key={col.key} className="py-3">
                                                {renderCell(item, col, false)}
                                            </td>
                                        );
                                    })}
                                </tr>

                                {/* Nested Rows */}
                                {hasNested && renderNestedRows(item, itemIndex)}
                            </React.Fragment>
                        );
                    })}
                </tbody>
            </table>
        </div>
    );
};