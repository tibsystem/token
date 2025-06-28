// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

contract FractionalPropertyToken {
    string public name;
    string public symbol;
    uint8 public constant decimals = 18;
    uint256 public totalSupply;
    address public owner;
    uint256 public buybackPrice;
    bool public buybackEnabled;

    bytes32 public DOMAIN_SEPARATOR;
    bytes32 public constant META_TRANSFER_TYPEHASH =
        keccak256("MetaTransfer(address from,address to,uint256 value,uint256 nonce)");

    mapping(address => uint256) public nonces;

    event MetaTransfer(address indexed relayer, address indexed from, address indexed to, uint256 value);
    event BuybackEnabled(uint256 price);
    event BuybackDisabled();

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
        totalSupply = _supply * (10 ** decimals);
        balanceOf[msg.sender] = totalSupply;
        DOMAIN_SEPARATOR = keccak256(
            abi.encode(
                keccak256(
                    "EIP712Domain(string name,string version,uint256 chainId,address verifyingContract)"
                ),
                keccak256(bytes(_name)),
                keccak256(bytes("1")),
                block.chainid,
                address(this)
            )
        );
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

    function metaTransfer(
        address from,
        address to,
        uint256 value,
        uint8 v,
        bytes32 r,
        bytes32 s
    ) public returns (bool) {
        require(to != address(0), "invalid to");

        bytes32 structHash = keccak256(
            abi.encode(
                META_TRANSFER_TYPEHASH,
                from,
                to,
                value,
                nonces[from]
            )
        );
        bytes32 digest = keccak256(
            abi.encodePacked("\x19\x01", DOMAIN_SEPARATOR, structHash)
        );
        address signer = ecrecover(digest, v, r, s);
        require(signer == from, "invalid signature");

        require(balanceOf[from] >= value, "balance too low");

        nonces[from] += 1;

        balanceOf[from] -= value;
        balanceOf[to] += value;
        emit Transfer(from, to, value);
        emit MetaTransfer(msg.sender, from, to, value);
        return true;
    }

    function enableBuyback(uint256 price) external onlyOwner {
        buybackPrice = price;
        buybackEnabled = true;
        emit BuybackEnabled(price);
    }

    function disableBuyback() external onlyOwner {
        buybackEnabled = false;
        emit BuybackDisabled();
    }

    /**
     * @dev Admin can forcefully buy back tokens from a list of investors.
     *      Payments are handled off-chain. This function only moves tokens
     *      back to the contract owner and emits standard Transfer events.
     * @param investors Array of investor addresses.
     * @param amounts   Corresponding token amounts to be transferred.
     */
    function adminBuyback(
        address[] calldata investors,
        uint256[] calldata amounts
    ) external onlyOwner {
        require(investors.length == amounts.length, "length mismatch");

        for (uint256 i = 0; i < investors.length; i++) {
            address investor = investors[i];
            uint256 amount = amounts[i];
            require(investor != address(0), "invalid investor");
            require(balanceOf[investor] >= amount, "balance too low");

            balanceOf[investor] -= amount;
            balanceOf[owner] += amount;
            emit Transfer(investor, owner, amount);
        }
    }

    function sellTokens(uint256 amount) external {
        require(buybackEnabled, "buyback not enabled");
        require(balanceOf[msg.sender] >= amount, "balance too low");

        balanceOf[msg.sender] -= amount;
        balanceOf[owner] += amount;
        emit Transfer(msg.sender, owner, amount);
        uint256 total = amount * buybackPrice;
        (bool sent, ) = msg.sender.call{value: total}("");
        require(sent, "Failed to send Ether");
    }

    function withdrawETH() external onlyOwner {
        payable(owner).transfer(address(this).balance);
    }

    receive() external payable {}
}
