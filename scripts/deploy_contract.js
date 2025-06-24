import { ethers } from 'ethers';
import solc from 'solc';
import fs from 'fs';

const rpcUrl = process.env.POLYGON_RPC_URL;

async function main() {
  const [solidityPath, privateKey, name, symbol, supply] = process.argv.slice(2);
  const source = fs.readFileSync(solidityPath, 'utf8');
  const input = {
    language: 'Solidity',
    sources: { 'Token.sol': { content: source } },
    settings: { outputSelection: { '*': { '*': ['abi', 'evm.bytecode'] } } }
  };
  const output = JSON.parse(solc.compile(JSON.stringify(input)));
  const contractName = Object.keys(output.contracts['Token.sol'])[0];
  const abi = output.contracts['Token.sol'][contractName].abi;
  const bytecode = output.contracts['Token.sol'][contractName].evm.bytecode.object;
  const provider = new ethers.JsonRpcProvider(rpcUrl);
  const wallet = new ethers.Wallet(privateKey, provider);
  const factory = new ethers.ContractFactory(abi, bytecode, wallet);
  const contract = await factory.deploy(name, symbol, BigInt(supply));
  await contract.waitForDeployment();
  const address = contract.target;
  console.log(JSON.stringify({ address, abi }));
}

main().catch((e) => { console.error(e); process.exit(1); });
