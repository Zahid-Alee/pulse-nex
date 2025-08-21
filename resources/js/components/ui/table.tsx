import * as React from "react";
import { cn } from "@/lib/utils";

type ThemeVariant = "white" | "blue" | "accent";

// Base table
const Table = React.forwardRef<
  HTMLTableElement,
  React.HTMLAttributes<HTMLTableElement> & { variant?: ThemeVariant }
>(({ className, variant = "white", ...props }, ref) => (
  <div className="w-full overflow-auto rounded-lg shadow-sm border border-border">
    <table
      ref={ref}
      className={cn(
        "w-full border-collapse text-sm",
        variant === "white" &&
          "bg-card text-card-foreground",
        variant === "blue" &&
          "bg-primary text-primary-foreground", 
        variant === "accent" &&
          "bg-accent text-accent-foreground",
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
      "border-b border-border",
      variant === "white" &&
        "bg-muted text-muted-foreground font-semibold",
      variant === "blue" && 
        "bg-primary/90 text-primary-foreground font-semibold",
      variant === "accent" && 
        "bg-accent/80 text-accent-foreground font-semibold",
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
      "divide-y",
      variant === "white" &&
        "divide-border",
      variant === "blue" && 
        "divide-primary-foreground/20",
      variant === "accent" && 
        "divide-accent-foreground/20",
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
      "transition-all duration-200 ease-in-out",
      variant === "white" &&
        "hover:bg-muted/50 data-[state=selected]:bg-muted",
      variant === "blue" && 
        "hover:bg-primary-foreground/10 data-[state=selected]:bg-primary-foreground/20",
      variant === "accent" && 
        "hover:bg-accent-foreground/10 data-[state=selected]:bg-accent-foreground/20",
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
      "px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider",
      variant === "white" &&
        "text-muted-foreground",
      variant === "blue" &&
        "text-primary-foreground/90",
      variant === "accent" &&
        "text-accent-foreground/90",
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
      "px-4 py-3 text-sm",
      variant === "white" &&
        "text-card-foreground",
      variant === "blue" &&
        "text-primary-foreground",
      variant === "accent" &&
        "text-accent-foreground",
      className
    )}
    {...props}
  />
));
TableCell.displayName = "TableCell";

// Table Footer (bonus component for completeness)
const TableFooter = React.forwardRef<
  HTMLTableSectionElement,
  React.HTMLAttributes<HTMLTableSectionElement> & { variant?: ThemeVariant }
>(({ className, variant = "white", ...props }, ref) => (
  <tfoot
    ref={ref}
    className={cn(
      "border-t font-medium",
      variant === "white" &&
        "bg-muted/50 text-muted-foreground border-border",
      variant === "blue" && 
        "bg-primary/80 text-primary-foreground border-primary-foreground/20",
      variant === "accent" && 
        "bg-accent/60 text-accent-foreground border-accent-foreground/20",
      className
    )}
    {...props}
  />
));
TableFooter.displayName = "TableFooter";

// Table Caption (bonus component for accessibility)
const TableCaption = React.forwardRef<
  HTMLTableCaptionElement,
  React.HTMLAttributes<HTMLTableCaptionElement> & { variant?: ThemeVariant }
>(({ className, variant = "white", ...props }, ref) => (
  <caption
    ref={ref}
    className={cn(
      "mt-4 text-sm",
      variant === "white" &&
        "text-muted-foreground",
      variant === "blue" &&
        "text-primary/70",
      variant === "accent" &&
        "text-accent/70",
      className
    )}
    {...props}
  />
));
TableCaption.displayName = "TableCaption";

export {
  Table,
  TableHeader,
  TableBody,
  TableRow,
  TableHead,
  TableCell,
  TableFooter,
  TableCaption
};