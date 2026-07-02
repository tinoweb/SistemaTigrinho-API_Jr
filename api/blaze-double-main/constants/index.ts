import { v4 } from "uuid";

export type Item = {
  number: number;
  color: string;
  textColor: string;
  id: string;
};

export const colors = [
  {
    name: "red",
    color: "red-700",
    placeHolder: "2x",
  },
  {
    name: "black",
    color: "black",
    placeHolder: "2x",
  },
  {
    name: "white",
    color: "white",
    placeHolder: "14x",
  },
];

export const items: Item[] = [
  ...Array.from({ length: 30 }, (_, i) => ({
    number: i + 1,
    color: i % 2 === 0 ? "bg-red-700" : "bg-black",
    textColor: "text-white",
    id: v4(),
  })),
  {
    number: 0,
    color: "bg-white",
    textColor: "text-black",
    id: v4(),
  },
  ...Array.from({ length: 30 }, (_, i) => ({
    number: i + 1,
    color: i % 2 === 0 ? "bg-red-700" : "bg-black",
    textColor: "text-white",
    id: v4(),
  })),
  {
    number: 0,
    color: "bg-white",
    textColor: "text-black",
    id: v4(),
  },
  ...Array.from({ length: 30 }, (_, i) => ({
    number: i + 1,
    color: i % 2 === 0 ? "bg-red-700" : "bg-black",
    textColor: "text-white",
    id: v4(),
  })),
  {
    number: 0,
    color: "bg-white",
    textColor: "text-black",
    id: v4(),
  },
  ...Array.from({ length: 30 }, (_, i) => ({
    number: i + 1,
    color: i % 2 === 0 ? "bg-red-700" : "bg-black",
    textColor: "text-white",
    id: v4(),
  })),
  {
    number: 0,
    color: "bg-white",
    textColor: "text-black",
    id: v4(),
  },
];
