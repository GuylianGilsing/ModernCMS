<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\APIs\Authentication\BlacklistTokens;

use DateTime;

use function ModernCMS\Core\APIs\Database\get_database_connection_instance;

/**
 * Blacklists a specific token ID.
 *
 * @param string $tokenId The ID of the token. This is usually a UUID.
 * @param string $type The type of the token.
 */
function blacklist_token(string $tokenId, string $type): bool
{
    $connection = get_database_connection_instance();
    $queryBuilder = $connection->createQueryBuilder();

    $affectedRows = $queryBuilder->insert('blacklisted_auth_tokens')
                                ->values([
                                    'token_id' => ':tokenId',
                                    'token_type' => ':tokenType',
                                    'blacklisted_at' => ':blacklistedTime'
                                ])
                                ->setParameter('tokenId', $tokenId)
                                ->setParameter('tokenType', $type)
                                ->setParameter('blacklistedTime', (new DateTime())->format('Y-m-d H:i:s'))
                                ->executeStatement();

    return $affectedRows > 0;
}

/**
 * Deletes any expired blacklisted token IDs with a specific type.
 *
 * @param string $type The type of the token.
 * @param int $lifeTimeInSeconds The life time of the token.
 *
 * @return int The number of rows affected.
 */
function delete_expired_blacklisted_token_ids(string $type, int $lifeTimeInSeconds): int
{
    $connection = get_database_connection_instance();
    $queryBuilder = $connection->createQueryBuilder();

    $blackListDate = (new DateTime())->modify("-{$lifeTimeInSeconds} seconds")->format('Y-m-d H:i:s');

    return $queryBuilder->delete('blacklisted_auth_tokens')
                        ->where(
                            $queryBuilder->expr()->and(
                                $queryBuilder->expr()->eq('token_type', ':tokenType'),
                                $queryBuilder->expr()->lt('blacklisted_at', ':blacklistedTime')
                            )
                        )
                        ->setParameter('tokenType', $type)
                        ->setParameter('blacklistedTime', $blackListDate)
                        ->executeStatement();
}
