# set up SparkContext for WordCount application
from pyspark import SparkContext
import itertools
sc = SparkContext("local", "BookPairs")

# the main map-reduce task
lines = sc.textFile("/home/cs143/data/goodreads.user.books")
pairs = lines.flatMap(lambda line: [x for x in itertools.combinations(line.split(":")[1].split(","), 2)])
integerPairs = pairs.map(lambda pair: tuple(int(x) for x in pair))
orderedPairs = integerPairs.map(lambda pair: (min(pair), max(pair)))
orderedPair1s = orderedPairs.map(lambda pair: (pair, 1))
pairCounts = orderedPair1s.reduceByKey(lambda a, b: a+b)
pairCounts20 = pairCounts.filter(lambda pairCount: pairCount[1] > 20)
pairCounts20.saveAsTextFile("output")
