<?hh

namespace Hack\UserDocumentation\API\Examples\MCRouter\MCrouterEx\GetOp;

function get_simple_mcrouter(): \MCRouter {
  $servers = Vector { \getenv('HHVM_TEST_MCROUTER') };
  $mc = \MCRouter::createSimple($servers);
  return $mc;
}

async function add_value(\MCRouter $mc, string $key,
                         string $value): Awaitable<void> {
  // can also pass optional int flags and int expiration time (in seconds)
  await $mc->add($key, $value);
}

async function run(): Awaitable<void> {
  $mc = get_simple_mcrouter();
  $unique_key = \str_shuffle('ABCDEFGHIJKLMN');
  await add_value($mc, $unique_key, "Hi");
  $val = await $mc->get($unique_key);
  try {
    // Shouldn't be able to add the same key twice
    await add_value($mc, $unique_key, "Bye");
  } catch (\MCRouterException $ex) {
    \var_dump($ex->getMessage());
    \var_dump($ex->getOp()); // will output an integer
    \var_dump(\MCRouter::getOpName($ex->getOp())); // will output friendlyt name
  }
}

\HH\Asio\join(run());
