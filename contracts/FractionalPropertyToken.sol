pragma solidity ^0.8.20;

contract FractionalPropertyToken {
    string public name;
    string public symbol;
    uint8 public decimals = 18;
    uint256 public totalSupply;
    address public owner;
    uint256 public buybackPrice;
    bool public buybackEnabled;

    mapping(address => uint256) public balanceOf;
    mapping(address => mapping(address => uint256)) public allowance;

    event Transfer(address indexed from, address indexed to, uint256 value);
    event Approval(address indexed owner, address indexed spender, uint256 value);

    modifier onlyOwner() {
        require(msg.sender == owner, "Not owner");
        _;
    }

    constructor(string memory _name, string memory _symbol, uint256 _supply) {
        name = _name;
        symbol = _symbol;
        owner = msg.sender;
        totalSupply = _supply;
        balanceOf[msg.sender] = _supply;
    }

    function transfer(address to, uint256 value) public returns (bool) {
        require(to != address(0), "invalid to");
        require(balanceOf[msg.sender] >= value, "balance too low");

        balanceOf[msg.sender] -= value;
        balanceOf[to] += value;
        emit Transfer(msg.sender, to, value);
        return true;
    }

    function approve(address spender, uint256 value) public returns (bool) {
        require(spender != address(0), "invalid spender");

        allowance[msg.sender][spender] = value;
        emit Approval(msg.sender, spender, value);
        return true;
    }

    function transferFrom(address from, address to, uint256 value) public returns (bool) {
        require(to != address(0), "invalid to");
        require(balanceOf[from] >= value, "balance too low");
        uint256 allowed = allowance[from][msg.sender];
        require(allowed >= value, "allowance too low");

        allowance[from][msg.sender] = allowed - value;
        balanceOf[from] -= value;
        balanceOf[to] += value;
        emit Transfer(from, to, value);
        emit Approval(from, msg.sender, allowance[from][msg.sender]);
        return true;
    }

    function enableBuyback(uint256 price) external onlyOwner {
        buybackPrice = price;
        buybackEnabled = true;
    }

    function disableBuyback() external onlyOwner {
        buybackEnabled = false;
    }

    function sellTokens(uint256 amount) external {
        require(buybackEnabled, "buyback not enabled");
        require(balanceOf[msg.sender] >= amount, "balance too low");

        balanceOf[msg.sender] -= amount;
        balanceOf[owner] += amount;
        emit Transfer(msg.sender, owner, amount);
        payable(msg.sender).transfer(amount * buybackPrice);
    }

    receive() external payable {}
}
