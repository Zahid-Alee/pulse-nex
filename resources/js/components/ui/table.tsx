import * as React from "react";
import { cn } from "@/lib/utils";

type ThemeVariant = "white" | "blue";

// Base table
const Table = React.forwardRef<
  HTMLTableElement,
  React.HTMLAttributes<HTMLTableElement> & { variant?: ThemeVariant }
>(({ className, variant = "white", ...props }, ref) => (
  <div className="w-full overflow-auto">
    <table
      ref={ref}
      className={cn(
        "w-full border-collapse text-sm",
        variant === "white" &&
          "bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100",
        variant === "blue" &&
          "bg-[#295BCC] text-white", 
        className
      )}
      {...props}
    />
  </div>
));
Table.displayName = "Table";

// Table Header
const TableHeader = React.forwardRef<
  HTMLTableSectionElement,
  React.HTMLAttributes<HTMLTableSectionElement> & { variant?: ThemeVariant }
>(({ className, variant = "white", ...props }, ref) => (
  <thead
    ref={ref}
    className={cn(
      variant === "white" &&
        "bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300",
      variant === "blue" && "bg-[#1F4AA5] text-white",
      className
    )}
    {...props}
  />
));
TableHeader.displayName = "TableHeader";

// Table Body
const TableBody = React.forwardRef<
  HTMLTableSectionElement,
  React.HTMLAttributes<HTMLTableSectionElement> & { variant?: ThemeVariant }
>(({ className, variant = "white", ...props }, ref) => (
  <tbody
    ref={ref}
    className={cn(
      variant === "white" &&
        "divide-y divide-gray-200 dark:divide-gray-700",
      variant === "blue" && "divide-y divide-[#1F4AA5]",
      className
    )}
    {...props}
  />
));
TableBody.displayName = "TableBody";

// Table Row
const TableRow = React.forwardRef<
  HTMLTableRowElement,
  React.HTMLAttributes<HTMLTableRowElement> & { variant?: ThemeVariant }
>(({ className, variant = "white", ...props }, ref) => (
  <tr
    ref={ref}
    className={cn(
      "transition-colors",
      variant === "white" &&
        "hover:bg-gray-100 dark:hover:bg-gray-800/50",
      variant === "blue" && "hover:bg-[#1F4AA5]/60",
      className
    )}
    {...props}
  />
));
TableRow.displayName = "TableRow";

// Table Head Cell
const TableHead = React.forwardRef<
  HTMLTableCellElement,
  React.ThHTMLAttributes<HTMLTableCellElement> & { variant?: ThemeVariant }
>(({ className, variant = "white", ...props }, ref) => (
  <th
    ref={ref}
    className={cn(
      "px-4 py-3 text-left font-medium",
      variant === "white" &&
        "text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700",
      variant === "blue" &&
        "text-white border-b border-[#1F4AA5]",
      className
    )}
    {...props}
  />
));
TableHead.displayName = "TableHead";

// Table Data Cell
const TableCell = React.forwardRef<
  HTMLTableCellElement,
  React.TdHTMLAttributes<HTMLTableCellElement> & { variant?: ThemeVariant }
>(({ className, variant = "white", ...props }, ref) => (
  <td
    ref={ref}
    className={cn(
      "px-4 py-3",
      variant === "white" &&
        "text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700",
      variant === "blue" &&
        "text-white border-b border-[#1F4AA5]",
      className
    )}
    {...props}
  />
));
TableCell.displayName = "TableCell";

export {
  Table,
  TableHeader,
  TableBody,
  TableRow,
  TableHead,
  TableCell
};
