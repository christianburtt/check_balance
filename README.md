# Check Balance

A proof of concept / Algorithm challenge.

Focused on DDD with OOP, there are different data streams/repositories for the same user who's completed
different actions in different platforms. We build a factory to synthesize the data and add it to an
aggregator entity who has a list of actions. In principle, it acts as a bit of a ledger.  Actions create points
but users can also withdraw points. So the ActionList aggregate is a master list of different "Accounts"

There are rules for users' actions to receive bonuses. These are in a Value Object of Boosters, which also would theoretically come from a data repository. The booster takes in its ruleset and compares against the appropriate list of actions to see if any bonus points should be given to the ActivityList

The Activity list then givees the total balance for the given user.

ASSUMPTIONS:
1) The different datastreams/repositories would be connected to the same user account in our system. Implemented by having the user's name in one of the json files
2) Boosters could be edited, turned on/off, and added in the future.
3) Users are able to withdraw, and that being the case, BOOSTED (aka Bonus) points are withdrawan FIRST. Obviously this could be different.

Code is built in vanilla PHP
Unit tests done with PHPUnit
"repositories" in this case are just the .json text files

The Performance of the main algorithm is O(m*n) where M is the number of booster rules and N is the number of datapoints / actions that apply to that booster rule


##Original Problem Statement:

You are developing the “Incentives Program.” Users can earn bonus points by performing specific actions. At the moment, there are three types of actions. Each of them results in a different amount of points which user receives:
- delivery: 1 point for every action
- rideshare: 1 point for every action
- rent: 2 points per day of the duration. 

There are also boosters in the system. Users can earn more points by doing “X” actions in a “Z” time frame. For example:
- 5 deliveries in 2 hours result in 5 additional points.
- 5 rideshares in 8 hours result in 10 additional points. 
- Rent has no boosters.
Each booster connects to a specific action type. So boosters for deliveries don’t apply to rideshares.

Each action can be part of only one booster, and boosters can be active only at a specific time. The system will be extended with new boosters in the future.

Points can have an expiry date. For example, points from boosters are valid only for one month and then lost unless the user withdraws them before the expiry date. Points for actions don’t expire at the moment. Users can cash out points with an exchange rate of 1 point equals 1 dollar.

For example:
Mark did seven deliveries in 2 hours, three rideshares in 4 hours, and rented a book for three days. His current balance is 21. After a month, his balance would shrink to 16.
