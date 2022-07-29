# Check Balance

A proof of concept / Algorithm challenge.

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

Code is built in vanilla PHP
Unit tests done with PHPUnit
"repositories" in this case are just the .json text files

The Performance of the main algorithm is O(m*n) where M is the number of rules and N is the number of datapoints / actions
