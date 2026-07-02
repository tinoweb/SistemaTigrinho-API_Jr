type ButtonProps = {
  color: string;
  text: string;
  onClick: () => void;
  selected?: boolean;
};

export const Button = ({
  color,
  text,
  onClick,
  selected = false,
}: ButtonProps) => {
  return (
    <button
      onClick={onClick}
      className={`bg-${color} w-[110px] ${
        color == "white" ? "text-black" : "text-white"
      } font-bold py-3 px-5 rounded-md mt-5 hover:opacity-100 transition-all text-sm ${
        selected ? "opacity-100 ring ring-white ring-offset-1" : "opacity-50"
      }`}
    >
      {text}
    </button>
  );
};
